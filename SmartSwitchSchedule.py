import schedule
import time
import requests
import json    
import io
import sys

try:
    to_unicode = unicode
except NameError:
    to_unicode = str
    
urlOff = 'https://maker.ifttt.com/trigger/SwitchOff/with/key/***'
urlOn = 'https://maker.ifttt.com/trigger/SwitchOn/with/key/***'


global period  
period = 0
def job():
    # Read JSON file
    with open('data.json') as data_file:
        data_loaded = json.load(data_file)
    global period 
    print(period)    
    if int(data_loaded['period']) != period:
        period = int(data_loaded['period'])
        sys.exit()
        #close the script
    if data_loaded['active'] == False:
        #print("off")
        return 1
    r = requests.post(urlOn)
    while r.status_code != requests.codes.ok:
        time.sleep(5)
        r = requests.post(urlOn)
    time.sleep(60*int(data_loaded['runTime']))
    r = requests.post(urlOff)
    while r.status_code != requests.codes.ok:
        time.sleep(5)
        r = requests.post(urlOff)
    

def job1():
    # Read JSON file
    with open('data.json') as data_file:
        data_loaded = json.load(data_file)
    global period  
    period = int(data_loaded['period'])
    print(period)
    
job1()   
job()

schedule.every(period).minutes.do(job)

while True:
    schedule.run_pending()
    time.sleep(1)
