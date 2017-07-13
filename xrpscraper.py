import requests
from bs4 import BeautifulSoup


def page():
    url = "https://coinmarketcap.com/currencies/ripple/historical-data/?start=20130428&end=20170713"
    source_code = requests.get(url)
    plain_text = source_code.text
    soup = BeautifulSoup(plain_text, "html5lib")
    for tr in soup.findAll('tr', {'class': 'text-right'}):
        td = tr.findAll('td')
        date = td[0]
        mk = td[6]
        mktcap = mk.string
        mktcap = mktcap.replace(',', '')
        file = open("xrp.html","a")
        file.write("\"" + date.string + "\":")
        file.write(mktcap + ",\n")
        file.close()

page()
