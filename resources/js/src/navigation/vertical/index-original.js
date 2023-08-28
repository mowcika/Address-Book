/*

Array of object

Top level object can be:
1. Header
2. Group (Group can have navItems as children)
3. navItem

* Supported Options

/--- Header ---/

header

/--- nav Grp ---/

title
icon (if it's on top level)
tag
tagVariant
children

/--- nav Item ---/

icon (if it's on top level)
title
route: [route_obj/route_name] (I have to resolve name somehow from the route obj)
tag
tagVariant

*/
import dashboard from './dashboard'
import master from './master'
import posting from './posting'
import freeCalling from './free-calling'
import emprIoTrack from './empr-ioTrack'
import appsAndPages from './apps-and-pages'
import others from './others'
import chartsAndMaps from './charts-and-maps'
import uiElements from './ui-elements'
import formAndTable from './forms-and-table'
import article from './article'
import forum from './forum'
import fseReport from './fse-report'
import excutive from './excutive'

// Array of sections
export default [...emprIoTrack, ...dashboard, ...master, ...posting, ...excutive, ...freeCalling, ...article, ...forum, ...fseReport, ...appsAndPages, ...uiElements, ...formAndTable, ...chartsAndMaps, ...others]
