



<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>
<script type = "text/javascript">

	function onConnectionLost()
	{
		console.log("connection lost");
		connected_flag=0;
	}
	
	function onFailure(message) 
	{
		console.log("Failed");

        setTimeout(MQTTconnect, reconnectTimeout);
    }
    
    
    var message_arrived='';
    var topic_name='';
	function onMessageArrived(r_message)
	{
		topic_name=r_message.destinationName;
		message_arrived=r_message.payloadString;
		
		
		data_getted_by_mqtt_to_canvas(topic_name,message_arrived);//===================> AS OUR FUNCTION
	}
	
	function onConnected(recon,url)
	{
		console.log(" in onConnected " +reconn);
	}
	
	function onConnect() 
	{
		connected_flag=1
		console.log("on Connect "+connected_flag);
	}

    function MQTTconnect(data) //data{host:'',port:'',client_name:'',username:'',password:''}
    {

		var s = data.host;
		var p = data.port;
		if (p!="")
		{
			console.log("ports");
			port=parseInt(p);
			console.log("port" +port);
		}
		if (s!="")
		{
			host=s;
			console.log("host");
		}
		console.log("connecting to "+ host +" "+ port);
		
		if(data.client_name===undefined || data.client_name=="")
		{
			var x=Math.floor(Math.random() * 10000); 
			var cname="orderform-"+x;
		}
		else
		{
			var cname=data.client_name;
		}
		
		mqtt = new Paho.MQTT.Client(host,port,cname);

		var options = {
		    timeout: 3,
			onSuccess: onConnect,
			onFailure: onFailure,
		 };
		
		if (data.username !="")
			options.userName=data.username;;
		if (data.password !="")
			options.password=data.password;;

		
		mqtt.onConnectionLost = onConnectionLost;
		mqtt.onMessageArrived = onMessageArrived;
		mqtt.onConnected = onConnected;
		
		
		mqtt.connect(options);
		return false;
	  
	 
	}
	
	function sub_topics(topics)//['topic1','topic2',...]
	{

		if (connected_flag==0)
		{
			out_msg="<b>Not Connected so can't subscribe</b>"
			console.log(out_msg);
			return false;
		}

		console.log("Subscribing to topics ...");

		topics.forEach((one_topic) => {
			console.log(one_topic);
			mqtt.subscribe(one_topic);
		});
		console.log("Subscribing OK ...");
		return false;
	}
	

	function publish_message(topic_name,txt_msg)
	{

		if (connected_flag==0)
		{
			out_msg="<b>Not Connected so can't send</b>"
			console.log(out_msg);

			return false;
		}
		
		message = new Paho.MQTT.Message(txt_msg);
		if (topic_name=="")
			message.destinationName = "test-topic";
		else
			message.destinationName = topic_name;
			
		mqtt.send(message);
	}

	
</script>


<script>
	var connected_flag=0;	
	var mqtt;
	var reconnectTimeout = 2000;
	var row=0;
	var out_msg="";
	var mcount=0;
	
	setTimeout(run_connection, 2000);
	
	function run_connection()
	{
    MQTTconnect({
    
      host:'broker.mqttdashboard.com',
      port:'8000', 
      client_name:'client_automated_building_inspection_system_'+String(Math.random()),
      username:'', 
      password:''
    
    }
    );
		
		setTimeout(function run_connection(){sub_topics(['project_topic/automated_building_inspection_system/location',]);}, 2000);
	}
	
	
</script>
 



