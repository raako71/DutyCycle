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
 
#var = {
#  "period": 30,
#  "runTime": 2,
#  "active": True,
#}

# Write JSON file
#with io.open('data.json', 'w', encoding='utf8') as outfile:
#    str_ = json.dumps(var,
#                      indent=4, sort_keys=True,
#                     separators=(',', ': '), ensure_ascii=False)
#   outfile.write(to_unicode(str_))

global period  
period = 0
def job():
    # Read JSON file
    with open('data.json') as data_file:
        data_loaded = json.load(data_file)
    global period 
    # print(period)  
    if int(data_loaded['period']) != period:
        period = int(data_loaded['period'])
        sys.exit()
        #close the script
    data_loaded['nextRun'] = int(time.time())+ 60*int(data_loaded['runTime']) + 60*int(data_loaded['period'])
    json.dump(data_loaded, open('data.json',"w"))
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
    # print(period)
    
#print(var == data_loaded)
#print(data_loaded)
#print(var['period'])

#s = requests.post(urlOn)
#print("On = ", s)
#time.sleep(60*data_loaded['runTime'])
#s = requests.post(urlOff)
#print("Off = ", s)

job1()   
job()

schedule.every(period).minutes.do(job)

while True:
    schedule.run_pending()
    time.sleep(1)
