
@echo off
:: nodejs��װĿ¼�µ�nodevars.bat
set nodevars = "D:\Program Files\nodejs\nodevars.bat"
:: �л���D��
d:
:: �ƶ�����Ҫ������Ŀ¼
cd xampp/product/jjj_food_chain/jjj_food_chain_shop
:: ������Ŀ
cmd /c %nodevars%&&npm run dev