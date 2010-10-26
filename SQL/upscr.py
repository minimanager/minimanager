#!/usr/bin/env python
import glob, os
patches = glob.glob('*.sql')
patches = sorted(patches)

for x in patches:
  #db = x.split("_")[2].replace('.sql', '')
  db = "mmfpm"
  os.system("mysql -u user -ppassword -v " + db + " < " + x)
