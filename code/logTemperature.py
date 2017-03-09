import os
import time

def readTemp():
	tempFile = open("/sys/bus/w1/devices/28-000006981b37/w1_slave")
	text = tempFile.read()
	currentTime = time.strftime('%x %X %Z')
	tempFile.close()
	tempC = float(text.split("\n")[1].split("t=")[1])/1000
	tempF = tempC * 9.0 / 5.0 + 32.0
	return [currentTime, tempC, tempF]
print readTemp()
