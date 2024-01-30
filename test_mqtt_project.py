import time
import paho.mqtt.client as paho
from paho import mqtt

import threading

import random


# setting callbacks for different events to see if it works, print the message etc.
def on_connect(client, userdata, flags, rc, properties=None):
    print("CONNACK received with code %s." % rc)

    if rc == 0:
      print("Connected to MQTT Broker!")
    else:
      print("Failed to connect, return code %d\n", rc)



def on_publish(client, userdata, mid, properties=None):
    print("publish OK => mid: " + str(mid))


def on_subscribe(client, userdata, mid, granted_qos, properties=None):
    print("Subscribed Ok => mid: " + str(mid) + " " + str(granted_qos))


def on_message(client, userdata, msg):
    subscribe_function(str(msg.topic),str(msg.payload))


client_name_id="set_here_cliente_id_"+str(random.randint(1, 100))+"_"+str(random.randint(1, 100))

client = paho.Client(client_id=client_name_id, userdata=None, protocol=paho.MQTTv31)
client.on_connect = on_connect


'''
# enable TLS for secure connection
client.tls_set(tls_version=mqtt.client.ssl.PROTOCOL_TLS)######
# set username and password
client.username_pw_set("yeczrtkd:yeczrtkd", "8GrG6YxV_fd1O40MvreyzIBGd_hfGta3")
'''

client.connect("mqtt-dashboard.com", 1883)
client.on_subscribe = on_subscribe
client.on_message = on_message
client.on_publish = on_publish

client.subscribe("any_topic_here_to_subsecribe/in/our/project", qos=1)


def subscribe_function(topic,msg):
  print("topic:"+topic+"  msg:"+msg)

def publish_mqtt(topic,msg):
  client.publish(topic, payload=msg, qos=1)


def demo_test_publish():

  while 1:

    #msg=str(random.randint(0, 500))+","+str(random.randint(0, 500))+","+['structural_defect','cracks'][random.randint(0, 1)]
    msg="0,3,cracks"
    publish_mqtt("project_topic/automated_building_inspection_system/location",msg)
    time.sleep(2)
    msg2="4.7,3.58,structural_defect"
    publish_mqtt("project_topic/automated_building_inspection_system/location",msg2)
    time.sleep(2)






t1 = threading.Thread(target=lambda :client.loop_forever(), args=())
t1.start()

demo_test_publish()