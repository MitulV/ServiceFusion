<!DOCTYPE html>
<html>
<head>
    <title>Exhale</title>
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>
<div class="container">
  @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<nav class="navbar navbar-default" style="margin-top: 2%">
  <div class="container-fluid">
    
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="http://localhost/webscraper/public">Exhale</a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        {{-- <li class="active"><a href="http://localhost/webscraper/public">Section A <span class="sr-only">(current)</span></a></li>
        <li><a href="http://localhost/webscraper/public/section-B">Section B</a></li> --}}
      </ul>
    </div>
  
  </div>

</nav>


   
    <div class="panel panel-primary">
      <div class="panel-heading"><h2>Upload Excel Here!</h2></div>
      <div class="panel-body">
        
        <!--@if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
                </div>
        <img src="uploads/{{ Session::get('file') }}">
        @endif -->
  
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('file.upload.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row" style="margin-top:2%">

                <div class="col-md-6">
                    <input type="file" name="file" class="form-control">
                </div>
   
                <div class="col-md-1">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary" onclick='window.location.reload(true)'>Reset</button>
                </div>

                <div class="col-md-3">
                  <a href="{{ route('getCustomers')}}">
                  <button type="button" class="btn btn-primary">Send Customer Emails<span style="margin-left: 5%"><i class="fa-solid fa-paper-plane">  
                  </i></span></button>
                  </a>
              </div>
    
            </div>
        </form>
  
      </div>
    </div>
</div>


</body>
</html>