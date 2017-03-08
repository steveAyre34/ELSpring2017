import RPi.GPIO as GPIO
import time

GPIO.setmode(GPIO.BCM)
GPIO.setup(18, GPIO.OUT)

def blink():
	count = 0
	while(count < 3):
		print "3 Blinks"
		for i in range(0, 3):
			print "Blink #" + str(i+1)
			GPIO.output(18, True)
			time.sleep(0.2)
			GPIO.output(18, False)
			time.sleep(0.2)
		print "Preparing 4 Blinks"
		time.sleep(5)
		for i in range(0, 4):
			print "Blink #" + str(i+1)
			GPIO.output(18, True)
			time.sleep(0.1)
			GPIO.output(18, False)
			time.sleep(0.1)
		count = count + 1
		print "------------"
		time.sleep(5)
		
	print "Done"
	GPIO.cleanup()
blink()
