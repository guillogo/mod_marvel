# Marvel lists #

This module use the Marvel API to display:

- Characters
- Comics
- Stories
- Events
- Creators

More information: https://developer.marvel.com/docs

## Configuration ##
You will need a public and private key to display the different lists. Add your keys in: `/admin/settings.php?section=mod_marvel`.

## Branches ##
The following maps the plugin version to use depending on your Moodle version.

| Moodle version    | Branch      |
| ----------------- | ----------- |
| Moodle 3.11+      | main        |


## Installing ##
1. Install the plugin the same as any standard moodle plugin either via the Moodle plugin directory, or you can use git to clone it into your source:

`git clone git@github.com:guillogo/mod_marvel.git mod/marvel`

2. Then run the Moodle upgrade

## Unit test ##
you need to edit the config.php file to add the following configuration information near the end, but before the `require_once(dirname(__FILE__) . '/lib/setup.php');`.

(Replace xxxxpublickeyxxxx with your publickey and xxxxprivatexxxx with your privatekey) 

```
define('MARVELTESTPRIVATEKEY', 'xxxxprivatexxxx');
define('MARVELTESTPUBLICKEY', 'xxxxpublickeyxxxx');
```
## License ##

2021 Guillermo Gomez <guigomar@gmail.com>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
