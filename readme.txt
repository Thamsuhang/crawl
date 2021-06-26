Author
Yoyal Limbu
Instruction
Place it in your localhost and run localhost/crawl
**************************************** My Approach *****************************************
Step1 : Got the area of practice and funding scheme id using phpquery from page ' https://www.otaus.com.au/find-an-ot';
Step2 : Loaded it on the projects index page
Step3 : Once form Submitted it sends a post request to "https://www.otaus.com.au/search/membersearchdistance" and in return gets the id of the individual data
Step4 :send another request to "https://www.otaus.com.au/search/getcontacts?ids=" appending the previous ids from step3
Step5: Crawl from the data using regex,classSelector and elements to format the given array
Step:6 Put the array to the data.csv file and download at last.

PS: MAX of 50 data's can only be downloaded at once.