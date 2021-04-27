<?php
	
	//IMPORTANDO ESSAS CLASSES// BIBLIOTECAS
	require "./bibliotecas/PHPMailer/Exception.php";
	require "./bibliotecas/PHPMailer/OAuth.php";
	require "./bibliotecas/PHPMailer/PHPMailer.php";
	require "./bibliotecas/PHPMailer/POP3.php";//trata do recebimento do email//trata do envio de email
	require "./bibliotecas/PHPMailer/SMTP.php";//trata do envio de email//trata do recebimento do email 

	//EXTRAINDO ESSES RECURSOS ABAIXO
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	//print_r($_POST);//recuperando os dados dos formularios/com o post, os dados estao invisiveis.

	class Mensagem {//criou atributos privados para não serem acessados por outros usúarios
		private $para = null;
		private $assunto = null;
		private $mensagem = null;
		//variavel criada para atender ao suceso ou não sucesso do envio do e-mail ou erro.
		public $status = array( 'codigo_status' => null, 'descricao_status' => '');

		public function __get($atributo) {//recuperando os atributos de Mensagem
			return $this->$atributo;
		}

		public function __set($atributo, $valor) {//processando os dados dos atributos para serem imprimidos 
			$this->$atributo = $valor;
		}

		public function mensagemValida() {//verificando se os dados estão vazios ou nao//ex:se usuario fez o cadastro do formulario ou nao, se faltar algum campo ele retorna Mensagem não é valida
			if(empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
				return false;
			}

			return true;//se for sim ele retorna Mensagem não é valida
		}
	}

	$mensagem = new Mensagem();//instancia de mensagem

	//aqui ele ta recuperando os dados de cada campo do fromulario
	$mensagem->__set('para', $_POST['para']);
	$mensagem->__set('assunto', $_POST['assunto']);
	$mensagem->__set('mensagem', $_POST['mensagem']);

	//print_r($mensagem);

	if(!$mensagem->mensagemValida()) {// CASO A MENSAGEM NAO FOR VALIDA !
		echo 'Mensagem não é válida';//se os campos nao forem prenchidos ele exibe essa mensagem
		header('Location: index.php');//impede que o usúario abra uma nova pagina do site sem ter feito o cadastro.
	}

	//TRATA DO ENVIO DE E-MAILE E TAMBEM NO ERRO DE ENVIO DE E-MAIL
	$mail = new PHPMailer(true);
	try {
	    //Server settings
	    $mail->SMTPDebug = false;                                 // Enable verbose debug output
	    $mail->isSMTP();                                      // Set mailer to use SMTP
	    $mail->Host = 'smtp.gmail.com';  //Obrigatorio alterar para esse host do google // Specify main and backup SMTP servers
	    $mail->SMTPAuth = true;                               // Enable SMTP authentication
	    $mail->Username = 'botar seu email por aqui';//O usúario deve botar o seu -email por aqui // SMTP username
	    $mail->Password = 'botar sua senha aqui';//importante botar sua senha de e-mail por aqui pra funcionar o envio // SMTP password
	    $mail->SMTPSecure = 'tls';  //Alterado para tls // Enable TLS encryption, `ssl` also accepted
	    $mail->Port = 587;                                    // TCP port to connect to

	    //Recipients
	    $mail->setFrom('botar seu email aqui', 'Erickson Completo Remetente');//aqui ele selcionou o e-mal onde sera enviado as mensagens
	    $mail->addAddress($mensagem->__get('para')); ;//aqui ele ta recuperando o e-mail prenchido no formulario (para), para ser enviado no e-mail do usuario selecionado. // Add a recipient
	    //$mail->addReplyTo('info@example.com', 'Information');
	    //$mail->addCC('cc@example.com');
	    //$mail->addBCC('bcc@example.com');

	    //Attachments
	    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	    //Content
	    $mail->isHTML(true); // Set email format to HTML                                 
	    $mail->Subject = $mensagem->__get('assunto');//aqui ele ta recuperando o asunto prenchido no formulario assunto para ser enviado no e-mail selecionado.
	    $mail->Body    = $mensagem->__get('mensagem');//aqui ele ta recuperando a mensagem prenchida no formulario mensagem para ser envida no email selecionado.
	    $mail->AltBody = 'Necessário utilizar um client que suporte HTML para ter acesso total ao conteúdo dessa mensagem';

	    $mail->send();

	    //se o email for envido com sucesso ele retoerba essa mensagem, E-mail enviado com sucesso
	    $mensagem->status['codigo_status'] = 1;
	    $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso';
	
	} catch (Exception $e) {

		//se der erro no envio ele retorna essa mensagem, Não foi possível enviar este e-mail 
		$mensagem->status['codigo_status'] = 2;
	    $mensagem->status['descricao_status'] = 'Não foi possível enviar este e-mail! : ' . $mail->ErrorInfo;

	    //alguma lógica que armazene o erro para posterior análise por parte do programador
	}
?>


<html><!-- aqui ele esta reutilizando os mesmo formato de estilizaçao do index.php -->
	<head>
		<meta charset="utf-8" />
    	<title>App Mail Send</title>

    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	</head>

	<body>

		<div class="container">
			<div class="py-3 text-center">
				<img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
				<h2>Send Mail</h2>
				<p class="lead">Seu app de envio de e-mails particular!</p>
			</div>

			<div class="row">
				<div class="col-md-12">

					<!--Caso a mensagen for enviada com sucesso -->
					<? if($mensagem->status['codigo_status'] == 1) { ?>

						<div class="container">
							<h1 class="display-4 text-success">Sucesso</h1>
							<p><?= $mensagem->status['descricao_status'] ?></p>
							<a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
						</div>

					<? } ?>

					<!--Caso a mensagen der erro e não for enviada -->
					<? if($mensagem->status['codigo_status'] == 2) { ?>

						<div class="container">
							<h1 class="display-4 text-danger">Ops!</h1>
							<p><?= $mensagem->status['descricao_status'] ?></p>
							<a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
						</div>

					<? } ?>

				</div>
			</div>
		</div>

	</body>
</html>

?>
