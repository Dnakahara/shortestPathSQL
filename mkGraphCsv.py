# -*- coding: utf-8 -*- 

import csv
import codecs
import random

v_csv_file = codecs.open('./GraphV.csv', 'w', 'utf-8')
writer = csv.writer(v_csv_file)

v_num = 5000
id = 0
while id < v_num:
    row = [-1];
    writer.writerow(row)
    id += 1;

v_csv_file.close()
e_csv_file = codecs.open('./GraphE.csv', 'w', 'utf-8')
writer = csv.writer(e_csv_file)

e_num = min(v_num * v_num / 2 / 5 * 3, v_num * 10);
id = 0
while id < e_num:
    Src = random.randint(1, v_num)
    Dest   = random.randint(1, v_num)
    Cost = random.randint(0, 100)
    row = [Src, Dest, Cost]
    writer.writerow(row)
    id += 1

e_csv_file.close()
