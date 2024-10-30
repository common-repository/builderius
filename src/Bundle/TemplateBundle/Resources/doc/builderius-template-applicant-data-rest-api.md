**POST**: /wp-json/wp/v2/builderius-template-applicant-data - Get Data of Builderius Template Applicant

Parameters

| Name |   Type  | Description | 
| ---- | ------- | ----------- | 
| url | string | Applicant url |
| params | object | Applicant parameters |

Input Example

```
{
  "url": "http://builderius.test/",
  "params": {
    "GET": [
      {
        "key": "s",
        "value": ""
      },
      {
      	"key": "post_type",
        "value": ["post", "test_post"]
      },
      {
      	"key": "taxonomy",
        "value": "category"
      },
      {
      	"key": "term",
        "value": "uncategorized"
      }
    ]
  }
}
```
