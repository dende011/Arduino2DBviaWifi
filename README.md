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

The more details can be obtained from [official github](https://github.com/esp8266/esp8266-wiki/wiki) or [datasheet](https://nurdspace.nl/File:ESP8266_Specifications_English.pdf).
***
#### Since the max current supplied by arduino which is only 50mA, fails to meet the features of ESP8266 ESP-01 above, a regulator is used to steps up current (while stepping down voltage).

### **LM1117-3.3 Regulator**


