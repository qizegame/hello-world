<?php
    ini_set('date.timezone','Asia/Shanghai');
    //error_reporting(E_ERROR);
    require_once "lib/WxPay.Api.php";
    require_once "WxPay.JsApiPay.php";
    require_once 'log.php';
    //初始化日志
    $logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
    $log = Log::Init($logHandler, 15);
    //①、获取用户openid
    $tools = new JsApiPay();
    $openId = $tools->GetOpenid();
    
?>

<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="./bootstrap.min.css">
	<script src="./jquery.min.js"></script>
	<script src="./bootstrap.min.js"></script>
	<title>张家港市红十字会</title>
	<link rel="stylesheet" href="./css.css">
</head>
<body>

	<!-- 这里禁止文本框内回车键提交form表单，提交时表单数据不进行编码 -->
	<form method="post" onkeydown="if(event.keyCode==13)return false;" enctype="multipart/form-data">
	<h5>感谢您对红十字会人道事业的爱心捐赠！</h5>
	<div class="container">
	<div class="row thumbnail">
			<p>捐赠金额</p>
			<input type="button" class="btn btn-default cheek" value="5元">
			<input type="button" class="btn btn-default" value="10元">
			<input type="button" class="btn btn-default" value="20元">
			<input type="button" class="btn btn-default" value="50元">
			<div class="input-group">
			  <input type="text" id="money" name="money" class="form-control other" placeholder="其他金额（0.01-5000.00）">
			  <span class="input-group-addon">元</span>
			</div>
	</div>
	<input type="text" class="form-control" placeholder="捐赠者姓名" id="usename">
	<input type="text" class="form-control" placeholder="捐赠者手机号" id="phone">
	<p>是否需要捐赠票据</p>
	<label class="radio-inline">
	  <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">
	    是
	</label>
	<label class="radio-inline">
	  <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2" checked>
	    否
	</label>
	<textarea class="form-control" rows="3" disabled="disabled" id="address">您需要先填写捐赠者姓名</textarea>
	<button type="submit" class="btn btn-danger btn-lg btn-block" onclick="callpay()">确认捐赠</button>
	<p>所有捐赠明细将定期在张家港市红十字会网站公布</p>
	<div class="footer"><a href="http://www.zjgredcross.com/">>>&nbsp张家港市红十字会官方网站&nbsp<<</a></div>
	</form>
	</div>
	<script src="./jscript.js"></script>
	<script type="text/javascript">
	
	//调用微信JS api 支付
	function jsApiCall()
	{
		//如果自选金额有数值，就取这个数值为金额
		if ($('.other').val()!=false) {
			var money = $('.other').val();
			money = parseFloat($('.other').val());
			money = Math.floor(money*100) / 100;
			money = money*100;
		}else{
			//否则，就取带cheek值的input的数值为金额
			var money = $(".cheek").attr("value"); 
			money = parseFloat(money);
			money = money*100;
		}

		if ($('#usename').val()!=false&&$('#address').val()!="捐赠者姓名") {
			var usename = $('#usename').val();
		}
		if ($('#phone').val()!=false&&$('#address').val()!="捐赠者手机号") {
			var phone = $('#phone').val();
		}
		if ($('#address').val()!=false&&$('#address').val()!="您需要先填写捐赠者姓名"&&$('#address').val()!="请填写票据邮寄地址") {
			var address = $('#address').val();
		}

		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php 
        		$input = new WxPayUnifiedOrder();
        		$input->SetBody("红十字捐款");
        		$input->SetAttach("真心的为人民");
        		$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        		$money = $_POST['money'];
        		$money = floatval($money);
        		$input->SetTotal_fee($money);
        		$input->SetTime_start(date("YmdHis"));
        		$input->SetTime_expire(date("YmdHis", time() + 600));
        		$input->SetNotify_url("http://www.qcyanyu.com/wxpay/notify.php");
        		$input->SetTrade_type("JSAPI");
        		$input->SetOpenid($openId);
        		$order = WxPayApi::unifiedOrder($input);
        		echo  $tools->GetJsApiParameters($order);
    		?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_msg);
			}
		);
	}
    
    	function callpay()
    	{
    		if (typeof WeixinJSBridge == "undefined"){
    		    if( document.addEventListener ){
    		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
    		    }else if (document.attachEvent){
    		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
    		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
    		    }
    		}else{
    		    jsApiCall();
    		}
    	}
	</script>
</body>
</html>