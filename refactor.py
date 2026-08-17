import re

file_path = 'resources/views/admin/bracket.blade.php'
with open(file_path, 'r') as f:
    lines = f.readlines()

import os
os.makedirs('resources/views/admin/partials', exist_ok=True)

# 1. Extract 3459 to 3605
with open('resources/views/admin/partials/bracket-js-manual-winners.blade.php', 'w') as f:
    f.writelines(lines[3458:3605])
lines[3458:3605] = ["@include('admin.partials.bracket-js-manual-winners')\n"]

# 2. Extract 3334 to 3400
with open('resources/views/admin/partials/bracket-js-roomtour.blade.php', 'w') as f:
    f.writelines(lines[3333:3400])
lines[3333:3400] = ["@include('admin.partials.bracket-js-roomtour')\n"]

# 3. Extract 1262 to 3187
with open('resources/views/admin/partials/bracket-js-main.blade.php', 'w') as f:
    f.writelines(lines[1261:3187])
lines[1261:3187] = ["@include('admin.partials.bracket-js-main')\n"]

# 4. Extract 867 to 1260
with open('resources/views/admin/partials/bracket-css.blade.php', 'w') as f:
    f.writelines(lines[866:1260])
lines[866:1260] = ["@include('admin.partials.bracket-css')\n"]

with open(file_path, 'w') as f:
    f.writelines(lines)

print("Refactoring complete!")
