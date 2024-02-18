## Outline 


1.Login / register(hidden) page 
2.Main page 
- Top: records of historical period times
    - prediction of next period arrival time
- Middle: add nearest period record 
    - start time
    - end time
- Bottom : syndromes of period 
    - Headache 
    - Sore muscle 
    - Crying etc….

3.educational resources page
Grid layout
tips of how to get through the period times


## Api endpoints:
- Registration
- Login
- Prediction for menstrual cycles 
- recent menstrual cycle recording 
- Symptom tracking 
- Educational resources


## PHP files
- register.php 
    - including validation and sanitization of input data
- login.php
    - OAuth login using existing credentials from fb or google
- update-user-information.php
- change-password.php
- verify-2fa.php ? (待定)
- cycle.php(待定)
- symptom-list.php
- save-symptoms.php
- education-resources-display.php

## database 
- userInformation table 
    - user_id
    - user_name
    - user_email
    - user_age
    - user_weight
    - user_height

- educational resource table
    - tip_id
    - tip_category
    - tip_url


- menstrual cycle table 
    - cycle_id pk
    - user_id (链接到个人信息表)
    - start_date
    - end_date
    - cycle_length
    
- symptoms table
    - id 
    - symptom_id  int 
    - cycle_id int 
    - symptom_type string
    - intensity int 
    - record_at ( timestamp)


- symptom type table
    - symptom_id 
    - symptom type 


## start frontend and server
npm run serve
php -S localhost:8000

