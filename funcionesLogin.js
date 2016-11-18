function Login() 
{

		var pagina=("adminLogin.php");
		var usuario = {Email: $("#email").val(), Pass: $("#password").val()};
		//var result ="";

		$.ajax({
        type: 'POST',
        url: pagina,
        dataType: "json",
        data: {usuario: usuario},
        async: true
    })
    .done(function (objJson) {

        if (!objJson.Exito) {
            alert(objJson.Mensaje);
            return;
        }

        window.location.href = "principal.php";

    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        alert(jqXHR.responseText + "\n" + textStatus + "\n" + errorThrown);
    });

		
}