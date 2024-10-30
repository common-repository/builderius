**GET**: /wp-json/wp/v2/builderius-global-settings - Get Builderius Global Settings for all Technologies

Output
```
{
  "<technology1>": {
    "settings": [
      {
        "name": "<cssSettingName>",
        "value": {
          "<templateType or all>": {
            "<responsiveMode or all>": {
              "i1": [
                {
                  "a1": "integer",
                  "a2": "--var-1",
                  "b2": 10
                }
              ]
            }
          }
        }
      },
      {
        "name": "<notCssSettingName>",
        "value": {
          "<templateType or all>": {
            "a1": 10
          }
        }
      }
    ]
  },
  "<technology2>": {
    "settings": [
      {
        "name": "<cssSettingName>",
        "value": {
          "<templateType or all>": {
            "<responsiveMode or all>": {
              "i1": [
                {
                  "a1": "integer",
                  "a2": "--var-2",
                  "b2": 20
                }
              ]
            }
          }
        }
      },
      {
        "name": "<notCssSettingName>",
        "value": {
          "<templateType or all>": {
            "a1": 20
          }
        }
      }
    ]
  }
}
```
responsiveMode will be specified for css settings only


**GET**: /wp-json/wp/v2/builderius-global-settings/{technology} - Get Builderius Global Settings for specific technology

Output

```
{
  "settings": [
    {
      "name": "<cssSettingName>",
      "value": {
        "<templateType or all>": {
          "<responsiveMode or all>": {
            "i1": [
              {
                "a1": "integer",
                "a2": "--var-1",
                "b2": 10
              }
            ]
          }
        }
      }
    },
    {
      "name": "<notCssSettingName>",
      "value": {
        "<templateType or all>": {
          "a1": 10
        }
      }
    }
  ]
}
```
responsiveMode will be specified for css settings only


**POST | PUT | PATCH**: /wp-json/wp/v2/builderius-global-settings - Set Builderius Global Settings for all Technologies

Input

```
{
  "<technology1>": {
    "settings": [
      {
        "name": "<cssSettingName>",
        "value": {
          "<templateType or all>": {
            "<responsiveMode or all>": {
              "i1": [
                {
                  "a1": "integer",
                  "a2": "--var-1",
                  "b2": 10
                }
              ]
            }
          }
        }
      },
      {
        "name": "<notCssSettingName>",
        "value": {
          "<templateType or all>": {
            "a1": 10
          }
        }
      }
    ]
  },
  "<technology2>": {
    "settings": [
      {
        "name": "<cssSettingName>",
        "value": {
          "<templateType or all>": {
            "<responsiveMode or all>": {
              "i1": [
                {
                  "a1": "integer",
                  "a2": "--var-2",
                  "b2": 20
                }
              ]
            }
          }
        }
      },
      {
        "name": "<notCssSettingName>",
        "value": {
          "<templateType or all>": {
            "a1": 20
          }
        }
      }
    ]
  }
}
```
responsiveMode should be specified for css settings only


**POST | PUT | PATCH**: /wp-json/wp/v2/builderius-global-settings/{technology} - Set Builderius Global Settings for specific Technology

Input

```
{
  "settings": [
    {
      "name": "<cssSettingName>",
      "value": {
        "<templateType or all>": {
          "<responsiveMode or all>": {
            "i1": [
              {
                "a1": "integer",
                "a2": "--var-1",
                "b2": 10
              }
            ]
          }
        }
      }
    },
    {
      "name": "<notCssSettingName>",
      "value": {
        "<templateType or all>": {
          "a1": 10
        }
      }
    }
  ]
}
```
responsiveMode should be specified for css settings only
