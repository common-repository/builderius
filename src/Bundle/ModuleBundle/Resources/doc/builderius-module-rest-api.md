**GET**: /wp-json/wp/v2/builderius-modules - Get Builderius Modules

Output

| Name |   Type  | Description |
| ---- | ------- | ----------- |
|  id  | integer | Unique identifier for the module |
| date | string  | The date the module was published, in the site's timezone |
| modified | string | The date the module was last modified, in the site's timezone
| name | string | The name of module |
| label | string | The label of module |
| status | string | A named status of module |
| config | array | The config for the module |
| global | boolean | Indicates is global module |
| author | array | Data about module author (not included by default) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| filter\[id] or filter\[id]\[eq] | integer | Limit result set to specific IDs |
| filter\[id]\[neq] | integer | Ensure result set excludes specific IDs |
| filter\[author] or filter\[author]\[eq] | integer | Limit result set to modules created by specific authors |
| filter\[author]\[neq] | integer | Ensure result set excludes modules created by specific authors |
| filter\[status] or filter\[status]\[eq] | string | Limit result set to modules assigned one or more statuses | publish, future, draft, pending, private, trash, auto-draft, inherit, any | any |
| filter\[status]\[neq] | string | Ensure result set excludes modules assigned one or more statuses | publish, future, draft, pending, private, trash, auto-draft, inherit |
| filter\[global] | boolean | Limit result set to modules with specific global value |
| page\[number] | integer | Current page of the collection | | 1 |
| page\[size] | integer | Maximum number of items to be returned in result set | 1...100 | 10 |
| include | string | A list of output fields which are not included by default | author |


**GET**: /wp-json/wp/v2/builderius-modules/{id} - Get Builderius Module

Output

| Name |   Type  | Description |
| ---- | ------- | ----------- |
|  id  | integer | Unique identifier for the module |
| date | string  | The date the module was published, in the site's timezone |
| modified | string | The date the module was last modified, in the site's timezone
| name | string | The name of module |
| label | string | The label of module |
| status | string | A named status of module |
| config | array | The config for the module |
| global | boolean | Indicates is global module |
| author | array | Data about module author (not included by default) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| include | string | A list of output fields which are not included by default | author |


**POST**: /wp-json/wp/v2/builderius-modules - Create Builderius Module

Input

| Name |   Type  | Description |
| ---- | ------- | ----------- |
| name | string | The name of module |
| label | string | The label of module |
| status | string | A named status of module |
| config | array | The config for the module |
| global | boolean | Indicates is global module |
| author | integer | author ID |


**POST|PUT|PATCH**: /wp-json/wp/v2/builderius-modules/{id} - Update Builderius Module

Input

| Name |   Type  | Description |
| ---- | ------- | ----------- |
| name | string | The name of module |
| label | string | The label of module |
| status | string | A named status of module |
| config | array | The config for the module |
| global | boolean | Indicates is global module |
| author | integer | author ID |


**DELETE**: /wp-json/wp/v2/builderius-modules/{id} - Delete Builderius Module

Arguments

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| force | boolean | Whether to bypass trash and force deletion | true or false | false |