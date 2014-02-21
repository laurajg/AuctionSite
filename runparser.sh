#! /bin/bash

rm -f sellersRel.dat itemsRel.dat biddersRel.dat categoryRel.dat bidsRel.dat
python my_parser.py /afs/ir/class/cs145/project/ebay_data/items-*.xml
sort category.dat | uniq > temp.dat && mv temp.dat category.dat
sort users.dat | uniq > temp.dat && mv temp.dat users.dat
sort bids.dat | uniq > temp.dat && mv temp.dat bids.dat
sort items.dat | uniq > temp.dat && mv temp.dat items.dat
