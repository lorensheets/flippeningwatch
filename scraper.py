import requests
from bs4 import BeautifulSoup


def page():
    url = "https://coinmarketcap.com/currencies/ethereum/historical-data/?start=20130428&end=20170704"
    source_code = requests.get(url)
    plain_text = source_code.text
    soup = BeautifulSoup(plain_text, "html5lib")
    for tr in soup.findAll('tr', {'class': 'text-right'}):
        td = tr.findAll('td')
        date = td[0]
        mktcap = td[5]
        file = open("eth.html","a")
        file.write("\"" + date.string + "\":")
        file.write("\"" + mktcap.string + "\",\n")
        file.close()

page()
