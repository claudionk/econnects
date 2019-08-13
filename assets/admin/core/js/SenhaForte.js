function validarSenhaForca(){
	var senha = document.getElementById('password').value;
	var forca = 0;

	/*Imprimir a senha*/
	/*document.getElementById("impSenha").innerHTML = "Senha " + senha;*/

	if((senha.length >= 4) && (senha.length <= 7)){
		forca += 10;

	}else if(senha.length > 7){
		forca += 25;
	}

	if((senha.length >= 5) && (senha.match(/[a-z]+/))){
		forca += 10;
	}

	if((senha.length >= 6) && (senha.match(/[A-Z]+/))){
		forca += 20;
	}

	if((senha.length >= 7) && (senha.match(/[@#$%&;*]/))){
		forca += 25;
	}

	if(senha.match(/([1-9]+)\1{1,}/)){
		forca += -25;
	}

	mostrarForca(forca);
}

function mostrarForca(forca){
	/*Imprimir a força da senha*/
	/*document.getElementById("impForcaSenha").innerHTML = "Força: " + forca;*/

	if(forca < 30 ){
		document.getElementById("erroSenhaForca").innerHTML = '<div class="progress" style="height: 5px;"><div class="progress-bar progress-bar-striped bg-progress-danger" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div>';
	}else if((forca >= 30) && (forca < 50)){
		document.getElementById("erroSenhaForca").innerHTML = '<div class="progress" style="height: 5px;"><div class="progress-bar bg-progress-warning" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div></div>';
	}else if((forca >= 50) && (forca < 70)){
		document.getElementById("erroSenhaForca").innerHTML = '<div class="progress" style="height: 5px;"><div class="progress-bar bg-progress-info" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div></div>';
	}else if((forca >= 70) && (forca < 100)){
		document.getElementById("erroSenhaForca").innerHTML = '<div class="progress" style="height: 5px;"><div class="progress-bar bg-progress-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div></div>';
	}
}