
"""
FILE: skeleton_parser.py
------------------
Author: Garrett Schlesinger (gschles@cs.stanford.edu)
Author: Chenyu Yang (chenyuy@stanford.edu)
Modified: 10/13/2012

Skeleton parser for cs145 programming project 1. Has useful imports and
functions for parsing, including:

1) Directory handling -- the parser takes a list of eBay xml files
and opens each file inside of a loop. You just need to fill in the rest.
2) Dollar value conversions -- the xml files store dollar value amounts in 
a string like $3,453.23 -- we provide a function to convert it to a string
like XXXXX.xx.
3) Date/time conversions -- the xml files store dates/ times in the form 
Mon-DD-YY HH:MM:SS -- we wrote a function (transformDttm) that converts to the
for YYYY-MM-DD HH:MM:SS, which will sort chronologically in SQL.
4) A function to get the #PCDATA of a given element (returns the empty string
if the element is not of #PCDATA type)
5) A function to get the #PCDATA of the first subelement of a given element with
a given tagname. (returns the empty string if the element doesn't exist or 
is not of #PCDATA type)
6) A function to get all elements of a specific tag name that are children of a
given element
7) A function to get only the first such child

Your job is to implement the parseXml function, which is invoked on each file by
the main function. We create the dom for you; the rest is up to you! Get familiar 
with the functions at http://docs.python.org/library/xml.dom.minidom.html and 
http://docs.python.org/library/xml.dom.html

Happy parsing!
"""

import sys
from xml.dom.minidom import parse
from re import sub

columnSeparator = "<"

# Dictionary of months used for date transformation
MONTHS = {'Jan':'01','Feb':'02','Mar':'03','Apr':'04','May':'05','Jun':'06',\
                'Jul':'07','Aug':'08','Sep':'09','Oct':'10','Nov':'11','Dec':'12'}

categoryRel = open('category.dat', 'w+')
bidsRel = open('bids.dat', 'w+')
usersRel =open('users.dat', 'w+')
itemsRel =open('items.dat', 'w+')

"""
Returns true if a file ends in .xml
"""
def isXml(f):
    return len(f) > 4 and f[-4:] == '.xml'

"""
Non-recursive (NR) version of dom.getElementsByTagName(...)
"""
def getElementsByTagNameNR(elem, tagName):
    elements = []
    children = elem.childNodes
    for child in children:
        if child.nodeType == child.ELEMENT_NODE and child.tagName == tagName:
            elements.append(child)
    return elements

"""
Returns the first subelement of elem matching the given tagName,
or null if one does not exist.
"""
def getElementByTagNameNR(elem, tagName):
    children = elem.childNodes
    for child in children:
        if child.nodeType == child.ELEMENT_NODE and child.tagName == tagName:
            return child
    return None

"""
Parses out the PCData of an xml element
"""
def pcdata(elem):
        return elem.toxml().replace('<'+elem.tagName+'>','').replace('</'+elem.tagName+'>','').replace('<'+elem.tagName+'/>','')

"""
Return the text associated with the given element (which must have type
#PCDATA) as child, or "" if it contains no text.
"""
def getElementText(elem):
    if len(elem.childNodes) == 1:
        return pcdata(elem) 
    return ''

"""
Returns the text (#PCDATA) associated with the first subelement X of e
with the given tagName. If no such X exists or X contains no text, "" is
returned.
"""
def getElementTextByTagNameNR(elem, tagName):
    curElem = getElementByTagNameNR(elem, tagName)
    if curElem != None:
        return pcdata(curElem)
    return ''

"""
Converts month to a number, e.g. 'Dec' to '12'
"""
def transformMonth(mon):
    if mon in MONTHS:
        return MONTHS[mon] 
    else:
        return mon

"""
Transforms a timestamp from Mon-DD-YY HH:MM:SS to YYYY-MM-DD HH:MM:SS
"""
def transformDttm(dttm):
    dttm = dttm.strip().split(' ')
    dt = dttm[0].split('-')
    date = '20' + dt[2] + '-'
    date += transformMonth(dt[0]) + '-' + dt[1]
    return date + ' ' + dttm[1]

"""
Transform a dollar value amount from a string like $3,453.23 to XXXXX.xx
"""

def transformDollar(money):
    if money == None or len(money) == 0:
        return money
    return sub(r'[^\d.]', '', money)

"""
Parses a single xml file. Currently, there's a loop that shows how to parse
item elements. Your job is to mirror this functionality to create all of the necessary SQL tables
"""
def parseXml(f):
    dom = parse(f) # creates a dom object for the supplied xml file
    """
    TO DO: traverse the dom tree to extract information for your SQL tables
    """
    Items = dom.documentElement
    writeDatFiles(Items)



def writeDatFiles(root):
	allitems = getElementsByTagNameNR(root, 'Item')
	for Item in allitems:
		""" This data goes in Items Relation (SELLER ID ALSO) """
		ID = Item.getAttribute("ItemID")
		ItemName= getElementTextByTagNameNR(Item, "Name")
			
		CurrentBid = getElementTextByTagNameNR(Item, "Currently")
		CurrentBid=transformDollar(CurrentBid)
		
		BuyPrice = getElementTextByTagNameNR(Item, "Buy_Price")
		BuyPrice=transformDollar(BuyPrice)
		if len(BuyPrice) == 0:
			BuyPrice = "NULL"
		
		FirstBid = getElementTextByTagNameNR(Item, "First_Bid")
		FirstBid=transformDollar(FirstBid)
			
		NumBids = getElementTextByTagNameNR(Item, "Number_of_Bids")
		
		StartDate = getElementTextByTagNameNR(Item, "Started")
		StartDate = transformDttm(StartDate)
		
		EndDate = getElementTextByTagNameNR(Item, "Ends")
		EndDate = transformDttm(EndDate)
		
		Description = getElementTextByTagNameNR(Item, "Description")
		
		""" This data goes in Users Relation """
		Seller=getElementByTagNameNR(Item, "Seller")
		UserID = Seller.getAttribute("UserID")
		Rating = Seller.getAttribute("Rating")
		Location = getElementTextByTagNameNR(Item, "Location")
		Country = getElementTextByTagNameNR(Item, "Country")
		
		""" Write seller data to users relation """
		usersRel.write(UserID + columnSeparator + Rating + columnSeparator + Location + columnSeparator + Country + '\n')
						
		""" Write item data to users relation """
		itemsRel.write(ID + columnSeparator + UserID + columnSeparator + ItemName + columnSeparator + CurrentBid + columnSeparator + BuyPrice + columnSeparator + FirstBid + columnSeparator + NumBids + columnSeparator + StartDate + columnSeparator + EndDate + columnSeparator + Description + '\n')
		
		""" Get all categories for an item and write to bidCategory relation"""
		Categories = getElementsByTagNameNR(Item, "Category")
		for Category in Categories:
			categoryData = getElementText(Category)
			categoryRel.write(ID + columnSeparator + categoryData + '\n')
		
		""" for each bid element get data and write bid data to bids relation and bidder data to users relation """
		allBids = getElementByTagNameNR(Item, "Bids")
		Bids = getElementsByTagNameNR(allBids, "Bid")
		
		for Bid in Bids:
			Bidder = getElementByTagNameNR(Bid, "Bidder")
			UserID = Bidder.getAttribute("UserID")
			Rating = Bidder.getAttribute("Rating")
			Location = getElementTextByTagNameNR(Bidder, "Location")
			if len(Location) == 0:
				Location = 'NULL'
			
			Country = getElementTextByTagNameNR(Bidder, "Country")
			if len(Country) == 0:
				Country = 'NULL'
				
			BidTime = getElementTextByTagNameNR(Bid, "Time")
			BidTime = transformDttm(BidTime)
			
			BidAmount = getElementTextByTagNameNR(Bid, "Amount")
			BidAmount = transformDollar(BidAmount)
			
			bidsRel.write(ID + columnSeparator + UserID + columnSeparator + BidTime + columnSeparator + BidAmount + '\n')
			 
			usersRel.write(UserID + columnSeparator + Rating + columnSeparator + Location + columnSeparator + Country + '\n')
	

	
"""
Loops through each xml files provided on the command line and passes each file
to the parser
"""
def main(argv):
    if len(argv) < 2:
        print >> sys.stderr, 'Usage: python skeleton_parser.py <path to xml files>'
        sys.exit(1)
    # loops over all .xml files in the argument
    for f in argv[1:]:
        if isXml(f):
            parseXml(f)
            print "Success parsing " + f

if __name__ == '__main__':
    main(sys.argv)

categoryRel.close()
bidsRel.close()
usersRel.close()
itemsRel.close()