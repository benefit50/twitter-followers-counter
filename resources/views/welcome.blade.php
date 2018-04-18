<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Twitter retweet followers counter</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

        <style>
            .alert ul {
                margin-bottom: 0;
            }

            .credits {
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="col-12">
                <div style="margin-top: 100px;">
                    <div class="form-group mainForm">
                        <label for="exampleInputEmail1">Tweet url:</label>
                        <input type="text" name="url" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
                        <small id="emailHelp" class="form-text text-muted">Example : https://twitter.com/durov/status/976083990938517509</small>
                    </div>
                    <div class="alert alert-danger errorMessages" style="display: none">
                        <ul>
                            <li></li>
                        </ul>
                    </div>
                    <button class="btn btn-primary fetchButton">Fetch</button>
                </div>
            </div>

            <div class="col-12">
                <div class="tweetData" style="margin-top: 50px;display: none;">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <h6 class="card-subtitle mb-2 text-muted"></h6>
                            <p class="card-text" style="color: red"></p>
                            <div class="alert alert-primary sourceAlert" role="alert" style="display: none"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="credits">
                    <p>godiacdan@gmail.com</p>
                </div>
            </div>
        </div>

        <script src="/js/handlers.js"></script>
    </body>
</html>
