import datetime
import sqlite3 as mydb
import sys

def readTime():
	# create connection to database file
	con = mydb.connect("testTime.db")

	with con:
		#initial split retrieves xdate from datetime
		myDate = str(datetime.datetime.now())
		splitDate = myDate.split(" ")
		xdate =  splitDate[0]

		#second split retireves xtime and replaces : with -
		splitTime =  splitDate[1].split(".")
		xtime = splitTime[0]
		xtime = xtime.replace(":", "-")

		#executes insert into database columns with xdate and xtime
		print xdate + " " + xtime
		cur = con.cursor()
		cur.execute('INSERT INTO date_data (xdate, xtime) VALUES (' +
				xdate + ', ' + xtime + ')')

readTime() 	
