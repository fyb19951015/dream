<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div class="panel panel-default">
        <div class="panel-body">
            <a href="{{ route('topics.create') }}" class="btn btn-success btn-block" aria-label="Left Align">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> 新建帖子
            </a>
        </div>
    </div>
    {{--活跃用户--}}
    @if (count($active_users))
        <div class="panel panel-default">
            <div class="panel-body active-users">

                <div class="text-center">活跃用户</div>
                <hr>
                @foreach ($active_users as $active_user)
                    <a class="media" href="{{ route('users.show', $active_user->id) }}">
                        <div class="media-left media-middle">
                            <img src="{{ $active_user->avatar }}" width="24px" height="24px" class="img-circle media-object">
                        </div>

                        <div class="media-body">
                            <span class="media-heading">{{ $active_user->name }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
    {{--资源推荐--}}
    @if (count($links))
        <div class="panel panel-default">
            <div class="panel-body active-users">

                <div class="text-center">资源推荐</div>
                <hr>
                @foreach ($links as $link)
                    <a class="media" href="{{ $link->link }}">
                        <div class="media-body">
                            <span class="media-heading">{{ $link->title }}</span>
                        </div>
                    </a>
                @endforeach

            </div>
        </div>
    @endif
</body>
</html>