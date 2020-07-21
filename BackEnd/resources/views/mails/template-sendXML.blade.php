<div style="text-align: center;">
    <img src="https://toppautomacao.com.br/public/images/banner_topp.png" alt="TOPP ERP" style="max-height: 250px;">
</div>

<h1>XMLs do mês: {{date('m/Y', strtotime($dados['mes']))}}</h1>

<strong>Cliente: {{$dados['emitente']}}</strong>
<br>
<strong>CNPJ/CPF: {{$dados['cnpj']}}</strong>

<hr>

<em>enviado em: {{date('d/m/Y H:i:s')}} - <a href="https://toppautomacao.com.br/topp-erp">TOPP Automação - TOPP ERP</a></em>
