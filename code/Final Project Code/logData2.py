#!/usr/bin/python
import os
import time
import sqlite3 as mydb
import sys
import random

# This function uses an Adafruit DS18B20 digital temperature sensor to detect
#  the temperature in degrees Celsius. The temperature is converted to 
#  Fahrenheit. The measurement is returned along with the time and date of measurement
def readTemp():
  tempfile = open("/sys/bus/w1/devices/28-000008ab85a7/w1_slave")
  tempfile_text = tempfile.read()
  temperature = float(tempfile_text.split("\n")[1].split("t=")[1])/1000
  temperature = round( (temperature * 9/5.0 + 32),2 )
  currentDate = time.strftime('%x')
  currentTime = time.strftime('%X %Z')
 
  return [currentDate, currentTime, temperature]

# This function calls readTemp to retrieve time, date and temperature. It also uses
#  a random number generator to produce a humidity measurement. All four data are then
#  stored into a database
def logTemperature():
  con = mydb.connect('/home/pi/Project/retrieveData2.db')
  with con:
    try:
      cur = con.cursor()
      # First check to see if the database is empty 
      cur.execute('select count(*) from data_collection')
      count = cur.fetchall()
      count = format(count[0][0])
      # If the database is empty, store default humidity value
      if count == '0':
         h = 44.0
      # If the database is not empty, use rand-num-gen to produce humidity value
      else:
         cur.execute('select max(date) from data_collection')
         date = cur.fetchall()
         date = format(date[0][0])
         cur.execute('select max(time) from data_collection where '
            'date =:_date', {"_date": date})
         time = cur.fetchall()
         time = format(time[0][0])
         cur.execute('select humidity from data_collection where date =:_date and '\
            'time =:_time' ,{"_date": date, "_time": time})
         h = cur.fetchall()
         h = float( format(h[0][0]) )
         # Add to the most recent humidity reading, a number between -0.5 and 0.5 
         h = h + round( random.uniform(-0.5,0.5),2 )
      [d,t,T]=readTemp()
      cur.execute('insert into data_collection values(?,?,?,?)',(d,t,T,h))
    except:
      print "Error!!"

logTemperature()
