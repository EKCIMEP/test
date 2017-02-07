var offset = 0;

function loadComments() {
    var xhr = new XMLHttpRequest();

    xhr.open('post', '/load', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    offset += 2;

    var body = 'offset=' + encodeURIComponent(offset);

    xhr.send(body);
    if (xhr.status != 200) {
        console.log(xhr.status + ': ' + xhr.statusText);
    } else {
        if (JSON.parse(xhr.responseText)['comments'].length < 2) {
            $('#load-more').remove();
        }
        commentsBody(JSON.parse(xhr.responseText)['comments']);
    }
}

function addComment(form) {
    var xhr = new XMLHttpRequest();

    xhr.open('post', '/comment/create', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var body = 'comment=' + encodeURIComponent(form.comment.value);

    xhr.send(body);
    if (xhr.status != 200) {
        console.log(xhr.status + ': ' + xhr.statusText);
    } else {
        var result = template(JSON.parse(xhr.responseText)['rating']);
        $('#comments-logout').append(result);
        form.reset();
    }
}

function template(data, nested) {
    var html = '';
    nested = nested ? nested : '';
    var time = new Date(data['created_at']);
    var rating = data['rating'] ? data['rating'] : 0;

    html += '<ul class="media-list media-list-' + data['id'] + '">';
    html += '<li class="media ' + nested + ' ">';
    html += '<a class="pull-left" href="#">';
    html += '<img class="media-object img-circle" src="' + data['avatar_url'] + '" alt="profile">';
    html += '</a>';
    html += '<div class="media-body">';
    html += '<div class="well well-lg">';
    html += '<h4 class="media-heading text-uppercase reviews">' + data['nickname'] + '</h4>';
    html += '<ul class="media-date text-uppercase reviews list-inline">';
    var date = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
    html += '<li class="dd">' + date + '</li>';
    var month = time.getUTCMonth() < 10 ? '0' + time.getUTCMonth() : time.getUTCMonth();
    html += '<li class="mm">' + month + '</li>';
    html += '<li class="aaaa">' + time.getFullYear() + '</li>';
    var min = time.getUTCMinutes() < 10 ? '0' + time.getUTCMinutes() : time.getUTCMinutes();
    html += '<li class="time">' + time.getUTCHours() + ':' + min + '</li>';
    html += '</ul>';
    html += '<p class="media-comment">' + data['comment'] + '</p>';

    if (!data['editable']) {
        html += '<button class="btn btn-circle text-uppercase" onclick="rating(' + data['id'] + ', this);">';
        html += '<span class="glyphicon glyphicon-heart"></span> ' + parseInt(rating);
        html += '</button>';
    } else {
        html += '<div class="likes text-uppercase">';
        html += '<span class="glyphicon glyphicon-heart"></span> ' + parseInt(rating);
        html += '</div>';
    }

    if (data['editable']) {
        html += '<button class="btn btn-default btn-circle text-uppercase" onclick="editComment(this);">';
        html += '<span class="glyphicon glyphicon-pencil"></span> Edit';
        html += '</button>';
        html += '<button class="btn btn-success btn-circle text-uppercase" onclick="saveComment(this, ' + data['id'] + ');" style="display: none">';
        html += '<span class="glyphicon glyphicon-send"></span> Summit comment';
        html += '</button>';
        html += '<button class="btn btn-danger btn-circle text-uppercase" onclick="deleteComment(' + data['id'] + ');">';
        html += '<span class="glyphicon glyphicon-ban-circle"></span> Delete';
        html += '</button>';
    }

    html += '<a class="btn btn-warning btn-circle text-uppercase" data-toggle="collapse" href="#reply-' + data['id'] + '">';
    html += '<span class="glyphicon glyphicon-comment"></span> ' + data['nested'].length + ' comment';
    html += '</a>';
    html += '<button class="btn text-uppercase glyphicon glyphicon-plus" onclick="nestedComment(this, ' + data['id'] + ');">';
    html += '</button>';
    html += '</div>';
    html += '</div>';
    if (data['nested'][0] !== undefined) {
        html += '<div class="collapse" id="reply-' + data['id'] + '">';
        data['nested'].map(function (obj) {
            html += template(obj, 'media-replied');
        });
        html += '</div>';
    }
    html += '</li>';
    html += '</ul>';

    return html;
}

function commentsBody(comments) {
    var result = '';

    comments.map(function (obj) {
        result += template(obj);
    });

    $('#comments-logout').append(result);
}

function rating(id, btn) {
    var xhr = new XMLHttpRequest();

    xhr.open('post', '/comment/rating', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var body = 'comment=' + encodeURIComponent(id);

    xhr.send(body);
    if (xhr.status != 200) {
        console.log(xhr.status + ': ' + xhr.statusText);
    } else {
        var r = JSON.parse(xhr.responseText);
        $(btn).html('<span class="glyphicon glyphicon-heart"></span> ' + r['rating']);
    }
}

function editComment(box) {
    $(box).css('display', 'none');
    var parent = $(box).closest('.well');

    var text = $(parent).children('.media-comment').text();

    $(parent).children('.media-comment').html('<textarea class="form-control" id="new-text-comment">' + text + '</textarea>');

    $(parent).children('.btn-success').css('display', 'inline-block');
}

function saveComment(box, id) {
    $(box).css('display', 'none');
    var parent = $(box).closest('.well');

    var text = $(parent).children('.media-comment').children('textarea').val();

    $(parent).children('.media-comment').html(text);

    $(parent).children('.btn-default').css('display', 'inline-block');

    var xhr = new XMLHttpRequest();

    xhr.open('post', '/comment/update', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var body = 'comment=' + encodeURIComponent(id) + '&text=' + encodeURIComponent(text);

    xhr.send(body);
    if (xhr.status != 200) {
        console.log(xhr.status + ': ' + xhr.statusText);
    } else {
        console.log(xhr.responseText);
    }
}

function deleteComment(id) {
    var xhr = new XMLHttpRequest();

    xhr.open('post', '/comment/delete', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var body = 'comment=' + encodeURIComponent(id);

    xhr.send(body);
    if (xhr.status != 200) {
        console.log(xhr.status + ': ' + xhr.statusText);
    } else {
        var parent = $('.media-list-' + id).closest('.collapse');
        onDeleteComment(parent);
        $('.media-list-' + id).remove();
        console.log(xhr.responseText);
    }
}

function nestedComment(box, id) {
    var parent = $(box).closest('.media');
    var html = '';
    html += '<form onsubmit="event.preventDefault();addNestedComment(this);" class="form-horizontal" id="nestedForm" role="form">';
    html += '<div class="form-group">';
    html += '<label for="comment" class="col-sm-2 control-label">Comment</label>';
    html += '<div class="col-sm-10">';
    html += '<textarea class="form-control" name="comment" id="comment" rows="5"></textarea>';
    html += '<input id="commentId" name="commentId" value="' + id + '" hidden="hidden" />';
    html += '</div>';
    html += '</div>';
    html += '<div class="form-group">';
    html += '<div class="col-sm-offset-2 col-sm-10">';
    html += '<button class="btn btn-success btn-circle text-uppercase" type="submit" id="submitComment"><span class="glyphicon glyphicon-send"></span> Summit comment</button>';
    html += '<a class="btn btn-danger btn-circle text-uppercase" onclick="cancelNestedComment();" id="cancelComment"><span class="glyphicon glyphicon-ban-circle"></span> Cancel </a>';
    html += '</div>';
    html += '</div>';
    html += '</form>';

    $('#nestedForm').remove();
    var place = $(parent).children('.collapse');
    if (place.length != 0) {
        $(html).insertBefore(place);
    } else {
        $(parent).append(html);
    }
}

function cancelNestedComment() {
    $('#nestedForm').remove();
}

function addNestedComment(form) {
    var xhr = new XMLHttpRequest();
    var parent = $(form).closest('.media');

    xhr.open('post', '/comment/create-nested', false);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var body = 'text=' + encodeURIComponent(form.comment.value) +
        '&comment=' + encodeURIComponent(form.commentId.value);

    xhr.send(body);
    if (xhr.status != 200) {
        console.log(xhr.status + ': ' + xhr.statusText);
    } else {
        var result = template(JSON.parse(xhr.responseText)['rating'], 'media-replied');
        $('#nestedForm').remove();
        var collapse = parent.children('.collapse');
        if (collapse.length !== 0) {
            $(parent.children('.collapse')).append(result);
        } else {
            $(parent).append('<div class="collapse" id="reply-' + form.commentId.value + '"></div>');
            $(parent.children('.collapse')).append(result);
        }

        onAddComment(form.commentId.value, parent);
    }
}

function onAddComment(id, parent) {
    var count = $(parent).children('#reply-' + id).children('ul').length;
    var child = parent[0].querySelector('.btn-warning');

    $(child).html('<span class="glyphicon glyphicon-comment"></span>' + count + ' comment');
}

function onDeleteComment(parent) {
    setTimeout(function () {
        var count = $(parent).children('ul').length;
        var par = $(parent).closest('.media');
        var child = par[0].querySelector('.btn-warning');

        $(child).html('<span class="glyphicon glyphicon-comment"></span>' + count + ' comment');
    }, 200)
}