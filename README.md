# **Arduino2DBviaWifi**
This is course project about arduino wireless communication.
## **Issue**
A real-time brightness monitoring solution on web implemented with arduino via WiFi.

## **Electronic Component**
| <ul><li>Breadboard * 1</li><li>Arduino UNO * 1</li><li>Resistor 10k Ω * 1</li><li>Photoresistance * 1</li><li>ESP8266 ESP-01 * 1</li><li>LM1117-3.3 Regulator * 1</li></ul> | <img src="https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/component.png" title="component" width="240"/> |
| :------| :------|

## **Photoresistance values reading**

### Layout

<img src="https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/photoR.png" width="360" title="photovaluesReading"/>

### Code
```arduino
int Pin = 2; // linking (photocell) to anallog pin 2
int Val = 0; // photocell variable

void setup() {
  Serial.begin(9600);
}

void loop() {
  // reading values from photocell and output to Serial Port
  Val = analogRead(Pin);
  Serial.println(Val);  
  delay(1000); //reading once a sec       
}

``` 
## **Result**
<img title="photoresult" src="https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/photoresult.png" width="360"/>

****
## **Wifi transmission**
### **ESP8266 ESP-01**
#### Basic Features
* VDDIO:  1.7V ~ 3.6V(V10)
* 802.11n, MCS7, POUT =+14dBm 135mA on a 3.3V power supply
* 802.11g, OFDM 54Mbps, POUT=+16dBm 145 mA on a 3.3V power supply
#### Firmware
|Version|Default Baud Rate|
|------|------|
|V0.9.2.2|9600|
|V0.9.5.2|115200|

>#### If it need to flashing a new firmware, [AT command Set](http://www.pridopia.co.uk/pi-doc/ESP8266ATCommandsSet.pdf) can be used.

The more details can be obtained from [official github](https://github.com/esp8266/esp8266-wiki/wiki) or [datasheet](https://nurdspace.nl/File:ESP8266_Specifications_English.pdf).


>#### Since the max current supplied by arduino which is only `50mA`, fails to meet the features of ESP8266 ESP-01 above, a regulator is used to steps up current (while stepping down voltage).

### **LM1117-3.3 Regulator**
|![lmdata](https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/lmdata.png)|![lm](https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/lm.png)|
| :------| :------|

The more details can be obtained from [datasheet](http://www.ti.com/lit/ds/symlink/lm1117.pdf).
### **Layout**

|ESP8266 ESP-01|Arduino Uno|LM1117-3.3|
|------|------|------|
|UTXD|RX||
|URXD|TX||
||5V|V_in|
||GND|GND|
|CH_PD||V_out|
|VCC||V_out|
|GND||GND|

<img title="layoutW" src="https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/LayoutW.png" width="360"/>

### **HTTP Request**
In the project, method `GET` is used to transfer data.

#### Request example code

```sh
GET /receive.php?{key}={value}&{key}={value} HTTP/1.1
       Host: {Target IP}
```
Other method can be see from [RFC2616](https://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html).

### Arduino code
#### Parameters setting
```arduino
#include <SoftwareSerial.h>

// light
int Pin = 2;
int Val = 0;

//connect
String SID = "wifi SSID";
String PWD = "wifi key";
String IP = "server IP";
String file = "php file(server side)";

boolean upload = false;

// connect 8 to TX of wifi esp8266
// connect 9 to RX of wifi esp8266
SoftwareSerial esp8266(8,9); // RX, TX
```
#### Initialize esp8266
```arduino
void init_wifi(){
  Serial.println("=======================================");
  Serial.println("|---  Esp8266 Setting  —|\n");

  sendCommand("AT+RST",5000); // reset module
  sendCommand("AT+CWMODE=1",2000); // 
  sendCommand("AT+CWJAP=\""+SID+"\",\""+PWD+"\"",5000);
  sendCommand("AT+CIPMUX=0",2000); 
 
  Serial.println("\n|---  Setting Finish  ---|");
  Serial.println("=======================================");
  Serial.println(" Please Enter :");
  Serial.println("( 1 )  \" o \" ---> Upload data");
  Serial.println("( 2 )  \" x \" ---> Stop uploading data");
  Serial.println("( 3 )  \" reset \" ---> Reset esp8266");
  Serial.println("( 4 )  \" AT+Command \" ---> Control esp8266");
  Serial.println("======================================="); 
}
```
<img src="https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/initial.png" title="initial" width="720"/>

#### Reading controle
```arduino
void loop() {
  
    Val = analogRead(Pin);  // reading values
  
    while(Serial.available())
    {
        String input = Serial.readString();
        //Serial.println(input);   
        
        if (input == "o")
        {
            Serial.println("\n|---  Upload Start  ---|");
            upload = true;
        }
        else if (input == "x")
        {
           Serial.println("\n|---  Upload Stop  ---|");
           upload = false;
        }
        else if (input == "reset")
        { 
           init_wifi();
           upload = false;
        }
 else
        {
          if(!upload)
            if(input.substring(0,2) =="AT"){
              esp8266.println(input);
            }
        }
        input = "";  
    }
```
<img src="https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/transfer.png" title="transfer" width="720"/>

#### Data transferring
```arduino
void uploadData()
{
  // convert to string
  // TCP connection
  String cmd = "AT+CIPSTART=\"TCP\",\"";
  cmd += IP; //host
  cmd += "\",80";
  esp8266.println(cmd);
   
  if(esp8266.find("Error")){
    Serial.println("AT+CIPSTART error");
    return;
  }
  
  // prepare GET string
  String getStr = "GET /"+file+"?value=";
  getStr += String(Val);
  getStr +=" HTTP/1.1\r\nHost:"+IP;
  getStr += "\r\n\r\n";
  // send data length
  cmd = "AT+CIPSEND=";
  cmd += String(getStr.length());
  esp8266.println(cmd);

  if(esp8266.find(">")){
    esp8266.print(getStr);
  }
  else{
    esp8266.println("AT+CIPCLOSE");
    // alert user
    Serial.println("AT+CIPCLOSE");
  }
  delay(2000);  
}
```
<img src="https://github.com/nightheronry/Arduino2DBviaWifi/blob/master/images/stopping.png" title="stopping" width="720"/>
