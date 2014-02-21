
drop table if exists Category;
drop table if exists Bid;
drop table if exists Users;
drop table if exists Item;

create table Category(ItemID int references Item(ItemID), cat text, PRIMARY KEY(ItemID, cat));

create table Bid(ItemID int references Item(ItemID), UserID text references Users(UserID), 
			timeBid text, Amount real check(Amount > 0 and Amount is not null),
			 PRIMARY KEY(ItemID, timeBid, UserID, Amount));
			 
create table Users(UserID text PRIMARY KEY, Rating int, Location text, Country text);


create table Item(ItemID int PRIMARY KEY, UserID text references Users(UserID),
			 itemName text, CurrentBid real check(CurrentBid>0 and CurrentBid>=FirstBid 
			 and CurrentBid is not null), BuyPrice real check((BuyPrice > 0 or BuyPrice
			is NULL) and BuyPrice>=FirstBid), FirstBid real check(FirstBid >=0 and 
			FirstBid is not null),  NumBids int check(NumBids>=0 and NumBids is not null), 
			StartDate text, EndDate text check(EndDate > StartDate), Description text);
			 
		
drop table if exists Time;
create table Time(currTime text);
insert into Time values("2001-12-20 00:00:01");

drop table if exists CurrUser;
create table CurrUser(currUser text);
insert into CurrUser values("247");