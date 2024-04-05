<!DOCTYPE html>
<html>
    <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
       <script>
       $("document").ready(function(){
           $("#submit").click(function(event){
                   event.preventDefault();
                   $.ajax({
                       method:"post",
                       url:"/loginbackend",
                       data:$("#login").serialize(),
                       datatype:"text",
                       success: function(response){
                           $("#description").text(response);
                       }        
                   },alert(status))
           })
           });
       </script>
    </head>
    <body>
    <form id ="login" >
       <input type="text" name="username" placeholder="Enter Your Username">
       <br>
       <input type="password" name = "pwd" placeholder = "Enter your password">
       <br>
       <button id="submit" type="submit">submit</button>
       <div id="description"></div>
    </body>
</html>