<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="{{ asset('/vendor/editora/img/favicon.ico') }}" rel="shortcut icon" />

    <link rel="stylesheet" href="{{ asset('/vendor/editora/css/welcome.css') }}">
    <title>Editora Login <?php //echo $title ?></title>
</head>
<body>
    <div class="welcome-page">
        <figure class="pic">
            <img src="{{ asset('/vendor/editora/img/welcome.jpg') }}" >
        </figure>

        <div class="enter">
            <div class="welcome">
                <div class="logo">
                    <img src="{{ asset('/vendor/editora/img/omalogin.png') }}" >
                </div>
                <div class="choose">
                    <form method="post" action="{{route('editora.login')}}" name="Form_login" class="form">
                        @csrf
                        <input type="hidden" name="p_action" value="login"/>
                        <div class="form-group">
                            <label for="p_username" class="form-label">{{ __('editora_lang::messages.login_label_username')}}</label>
                            <input type="text" name="p_username" id="p_username" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="p_password" class="form-label">{{ __('editora_lang::messages.login_label_password')}}</label>
                            <input type="password" id="p_password" name="p_password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="u_lang" class="form-label">{{ __('editora_lang::messages.login_label_language')}}</label>
                            <select name="u_lang" onChange="javascript:changeLang(this.options[this.selectedIndex].value)" id="u_lang"  class="form-control">
                                @foreach( $array_langs as $menuLang )
                                    <option @if($lg==$menuLang) selected="true" @endif value="{{ $menuLang }}">
                                        {{ __('editora_lang::messages.language_choose_'.$menuLang) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="btn-row">
                            <button type="submit" class="btn">{{ __('editora_lang::messages.login_label_submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
            @if(session('error_login'))
                <div class="enter">
                    <p>{{session('error_login')}}</p>
                </div>
                @php(session()->forget('error_login'))
            @endif
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>
