 PRAGMA foreign_keys = ON;
    drop trigger if exists R1;
    drop trigger if exists R2;
     drop trigger if exists R3;
    drop trigger if exists R4;
     drop trigger if exists R5;
    drop trigger if exists R6;
    drop trigger if exists R7;
    drop trigger if exists R8;
    drop trigger if exists R9;
    drop trigger if exists R10;
    drop trigger if exists R11;
    drop trigger if exists R12;
    drop trigger if exists R13;
    drop trigger if exists R14;
    drop trigger if exists R15;

    
    /* Create trigger for if bid with invalid time inserted */
    create trigger R1
    after insert on Bid
    when (New.timeBid < (select StartDate from Item where ItemID=New.ItemID)
     or New.timeBid > (select EndDate from Item where ItemID=New.ItemID))
    begin
       select raise(rollback, 'invalid bid time insertion attempt (before or after auction end)');
    end;

    
    /* Create trigger to make sure any change to Bid hasn't resulted in an invalid time */
    create trigger R2
    after update on Bid
    when (New.timeBid < (select StartDate from Item where ItemID=New.ItemID)
     or New.timeBid > (select EndDate from Item where ItemID=New.ItemID))
    begin
       select raise(rollback, 'invalid bid time update attempt (before or after auction end)');
    end;
    
    /* Check if time of newly inserted bid is (illegally) in the future */
    create trigger R3
    after insert on Bid
    when New.timeBid > (select * from Time)
    begin
       select raise(rollback, 'invalid bid time insertion attempt (after current time)');
    end;


	create trigger R7
    after delete on Bid
    begin
      update Item
      set NumBids = NumBids-1
      where ItemID = Old.ItemID;
    end;

	/* If the new bid is the largest bid then update the current bid to the new bid */
	 /*	In every auction the number-of-bids field (if included) corresponds to the actual number of bids. */
    /* update number of bids if new bid inserted */
    create trigger R8
    after insert on Bid
    begin
      update Item
      set CurrentBid = New.Amount
      where ItemID = New.ItemID and New.Amount>CurrentBid;
      
      update Item
      set NumBids = NumBids+1
      where ItemID = New.ItemID;
    end;
    
    
     /* The amount of a new bid must be greater than that auction's current bid */
    create trigger R5
    after insert on Bid
    when New.Amount < (select CurrentBid from Item where ItemID = New.ItemID)
    begin
       select raise(rollback, 'invalid bid time insertion attempt (not bigger than the current bid)');
    end;
    
    /* 	A user can't bid on their own auction */
	create trigger R9
	after insert on Bid
	when New.UserID = (select UserID from Item where ItemID = New.ItemID)
	begin
		select raise(rollback, 'you can not bid on your own auction!');
	end;
    
    /* If the current bid is the buy price then the auction doesn't accept bids*/
    create trigger R10
    after insert on Bid
    when ((select CurrentBid from Item where ItemID=New.ItemID) = (select BuyPrice
    	from Item where ItemID=New.ItemID)) and (select count(*) from Bid where Bid.ItemID=New.ItemID)>0
    begin
    	select raise(rollback, 'Someone already bought this item (using buy price) this auction is closed');
    end;

	/* If current bid is buy price end date changed to current time
	create trigger R11
	after insert on Bid
	 when New.Amount = (select BuyPrice from Item where ItemID=New.ItemID) 
    begin
    	 update Item
      	set EndDate = (select currTime from Time)
      	where ItemID = New.ItemID;
    end; */

	/* make sure anyone opening an auction has a location and country */
	create trigger R13
	after insert on Item
	when (select count(*) from Users where New.UserID=Users.UserID and (Users.Location="" or Users.Country="")) > 0
	begin
		select raise(rollback, 'You need to have a location and country in order to sell an item');
	end;
    
    /* Check if an update to bid time in the bid table resulted in any bids having a bid time in the future */
    create trigger R4
    after update of timeBid on Bid
    when New.timeBid > (select * from Time)
    begin
       select raise(rollback, 'invalid bid time update attempt (after current time)');
    end;
    
    /* NEED TO UPDATE CURRENT BID, NUM BIDS WHEN YOU CHANGE THE CURRENT TIME */
    
  	create trigger R12
    after update of currTime on Time
    begin
    	update Item 
    	set NumBids = (select count(ItemID) from Bid B1 where B1.ItemID= Item.ItemID and
    				   B1.timeBid<=New.currTime and not exists(select * from Bid B2 where 
    				   B1.ItemID=B2.ItemID and B2.timeBid <= B1.timeBid and B2.Amount > B1.Amount)
    				  and not exists (select * from Bid B3 join Item using(ItemID) where
    				  B1.ItemID=B3.ItemID and B3.timeBid<B1.timeBid and  B3.Amount=Item.BuyPrice) 
    				  and not exists(select * from Bid B4 where B4.ItemID=B1.ItemID and B4.timeBid < B1.timeBid and B4.Amount=B1.Amount) 
    				   ); 
    	
    	update Item
    	set CurrentBid = (select max(Amount) from Bid B1 where B1.ItemID=Item.ItemID and 
    					 timeBid <= (select currTime from Time) and not exists (select * from Bid B3 join Item using(ItemID) where
    				  B1.ItemID=B3.ItemID and B3.timeBid<B1.timeBid and  B3.Amount=Item.BuyPrice) )
       	where Item.NumBids > 0;
       	
       	update Item
       	set CurrentBid=FirstBid
       	where Item.NumBids =0;
    end;
    
    create trigger R14
    after insert on Bid
    when exists (select * from Bid B1 where B1.ItemID=New.ItemID and B1.Amount=New.Amount and B1.timeBid<New.timeBid)
    begin
    	select raise(rollback, "Sorry someone already bid that amount for this item");
    end;
    
    /* enforce that can't have user bidding twice at same time but allows for time travel invalidating certain bids and so freeing up that time*/
    create trigger R15
    after insert on Bid
    when exists (select * from Bid B1 where B1.UserID=New.UserID and B1.timeBid=New.timeBid and B1.Amount<>New.Amount  and not exists (select * from Bid B2 where B2.ItemID=B1.ItemID and B2.timeBid<B1.timeBid and B2.Amount > B1.Amount))
    begin
    	select raise(rollback, "Sorry, you have already placed a bid at this time");
    end;
     
     /* enforce that can't have two bids on an item at same time but allows for time travel invalidating certain bids and so freeing up that time*/
     create trigger R16
     after insert on Bid
     when exists (select * from Bid B1 where B1.ItemID=New.ItemID and B1.timeBid=New.timeBid and B1.Amount<>New.Amount  and not exists (select * from Bid B2 where B2.ItemID=B1.ItemID and B2.timeBid<B1.timeBid and B2.Amount > B1.Amount))
     begin
    	select raise(rollback, "Sorry, there was already a bid on this item at this time");
    end;