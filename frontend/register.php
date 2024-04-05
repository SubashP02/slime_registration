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
                    url:"/register",
                    data:$("#register").serialize(),
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
    <form id="register" >
        <input type="text" name="username" placeholder="Username">
        <br>
        <input type="email" name="email" placeholder="Email">
        <br>
        <input type="text" name="phone_no" placeholder="mobile_number">
        <br>
        <input type="password" name="password" placeholder="Password">
        <br>
        <button id="submit" type="submit">Register</button>
    </form>
    <div id="description"></div>
    <a href="/login">Already Register</a>
    </body>
</html>