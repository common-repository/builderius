**GET**: /wp-json/wp/v2/builderius-templates - Get Builderius Templates

Output
```
[
  0: {
    data: {
      type: "builderius-templates",
      id: "1",
      attributes: {
        name: "template_for_all_singular",
        title: "Template for all singular",
        status: "draft",
        created_at: "2020-11-04T14:23:13",
        updated_at: "2020-11-10T14:22:06",
        sort_order: "10",
        apply_rules_config: {
          theme: {
            and: [
              0: {
                var: "singular"
              },
              1: {
                some: [
                  0: {var: "all"},
                  1: true
                ]
              }
            ]
          }
        },
        active_branch: "master",
        published_branch: null,
        type: "singular",
        technology: "html"
      },
      relationships: { 
        branches: {
          "data": [
            {
              "type": "builderius-template-branches",
              "id": "2"
            }
          ]
        },
        author: {
          data: {
            type: "users",
            id: "1"
          }
        }   
      }
    }
  },
  1: {
    data: {
      type: "builderius-templates",
      id: "2",
      attributes: {
        name: "template_for_all_archives",
        title: "Template for all archives",
        status: "draft",
        created_at: "2020-11-04T14:23:13",
        updated_at: "2020-11-10T14:22:06",
        sort_order: "10",
        apply_rules_config: {
          theme: {
            and: [
              0: {
                var: "archive"
              },
              1: {
                some: [
                  0: {var: "all"},
                  1: true
                ]
              }
            ]
          }
        },
        active_branch: "master",
        published_branch: null,
        type: "collection",
        technology: "html"
      },
      relationships: { 
        branches: {
          "data": [
            {
              "type": "builderius-template-branches",
              "id": "3"
            }
          ]
        },
        author: {
          data: {
            type: "users",
            id: "1"
          }
        }   
      }
    }
  }
]
```

| Name |   Type  | Description |
| ---- | ------- | ----------- |
|  id  | integer | Unique identifier for the template |
| created_at | string  | The date the template was created, in the site's timezone |
| updated_at | string | The date the template was last modified, in the site's timezone
| name | string | The name of template |
| title | string | The title of template |
| status | string | A named status of template |
| sort_order | integer | sort order of template |
| active_branch | string | Template active branch name |
| published_branch | string | Template published branch name |
| apply_rules_config | object | Template Apply Rules Config |
| branches | array | Template branches data (by default will be included just id, but if in request will be used `include=branches` filter - will be provided all information ) |
| type | string |  A type of template |
| technology | string |  A technology of template |
| author | object | Data about template author (by default will be included just id, but if in request will be used `include=author` filter - will be provided all information ) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| filter\[id] or filter\[id]\[eq] | integer | Limit result set to specific IDs |
| filter\[id]\[neq] | integer | Ensure result set excludes specific IDs |
| filter\[name] or filter\[name]\[eq] | string | Limit result set to specific names |
| filter\[name]\[neq] | string | Ensure result set excludes specific names |
| filter\[author] or filter\[author]\[eq] | integer | Limit result set to templates created by specific authors |
| filter\[author]\[neq] | integer | Ensure result set excludes templates created by specific authors |
| filter\[status] or filter\[status]\[eq] | string | Limit result set to templates assigned one or more statuses | publish, future, draft, pending, private, trash, auto-draft, inherit, any | any |
| filter\[status]\[neq] | string | Ensure result set excludes templates assigned one or more statuses | publish, future, draft, pending, private, trash, auto-draft, inherit |
| filter\[type] or filter\[type]\[eq] | string | Limit result set to templates with specific types |
| filter\[type]\[neq] | string | Ensure result set excludes templates with specific types |
| filter\[technology] or filter\[technology]\[eq] | string | Limit result set to templates with specific technologies |
| filter\[technology]\[neq] | string | Ensure result set excludes templates with specific technologies |
| page\[number] | integer | Current page of the collection | | 1 |
| page\[size] | integer | Maximum number of items to be returned in result set | 1...100 | 10 |
| include | string | A list of output fields which are not included by default | branches, author |

Output example for request `/wp-json/wp/v2/builderius-templates?filter[type]=collection&include=branches`
```
[
  0: {
    data: {
      type: "builderius-templates",
      id: "2",
      attributes: {
        name: "template_for_all_archives",
        title: "Template for all archives",
        status: "draft",
        created_at: "2020-11-04T14:23:13",
        updated_at: "2020-11-10T14:22:06",
        sort_order: "10",
        apply_rules_config: {
          theme: {
            and: [
              0: {
                var: "archive"
              },
              1: {
                some: [
                  0: {var: "all"},
                  1: true
                ]
              }
            ]
          }
        },
        active_branch: "master",
        published_branch: null,
        type: "collection",
        technology: "html"
      },
      relationships: { 
        branches: {
          "data": [
            {
              "type": "builderius-template-branches",
              "id": 3
            }
          ]
        },
        author: {
          data: {
            type: "users",
            id: 1
          }
        }   
      }
    },
    included: [
      0: {
        type: "builderius-template-branches",
        id: 3
        attributes: {
          name: "master",
          created_at: "2020-11-04T14:23:13",
          updated_at: "2020-11-10T14:22:06",
          base_branch: null,
          base_commit: null,
          active_commit: "u9cf96d85",
          published_commit: null,
          not_committed_config: null
        },
        relationships: {
          author: {
            data: {
              type: "users",
              id: 1
            }
          },
          template: {
            data: {
              type: "builderius-templates",
              id: 1
            }
          },
          commits: {
            data: [
              0: {
                type: "builderius-template-commits"
                id: 1
              }
            ]
          }
        }
      }
    ]
  }
]
```

**GET**: /wp-json/wp/v2/builderius-templates/{id} - Get Builderius Template

Output

```
{
  data: {
    type: "builderius-templates",
    id: "1",
    attributes: {
      name: "template_for_all_singular",
      title: "Template for all singular",
      status: "draft",
      created_at: "2020-11-04T14:23:13",
      updated_at: "2020-11-10T14:22:06",
      sort_order: "10",
      apply_rules_config: {
        theme: {
          and: [
            0: {
              var: "singular"
            },
            1: {
              some: [
                0: {var: "all"},
                1: true
              ]
            }
          ]
        }
      },
      active_branch: "master",
      published_branch: null,
      type: "singular",
      technology: "html"
    },
    relationships: { 
      branches: {
        "data": [
          {
            "type": "builderius-template-branches",
            "id": "2"
          }
        ]
      },
      author: {
        data: {
          type: "users",
          id: "1"
        }
      }   
    }
  }
}
```

| Name |   Type  | Description |
| ---- | ------- | ----------- |
|  id  | integer | Unique identifier for the template |
| created_at | string  | The date the template was created, in the site's timezone |
| updated_at | string | The date the template was last modified, in the site's timezone
| name | string | The name of template |
| title | string | The title of template |
| status | string | A named status of template |
| sort_order | integer | sort order of template |
| active_branch | string | Template active branch name |
| published_branch | string | Template published branch name |
| apply_rules_config | object | Template Apply Rules Config |
| branches | array | Template branches data (by default will be included just id, but if in request will be used `include=branches` filter - will be provided all information ) |
| type | string |  A type of template |
| technology | string |  A technology of template |
| author | object | Data about template author (by default will be included just id, but if in request will be used `include=author` filter - will be provided all information ) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| include | string | A list of output fields which are not included by default | branches, author |


**POST**: /wp-json/wp/v2/builderius-templates - Create Builderius Template

Input

```
{
  data: {
    type: "builderius-templates",
    attributes: {
      name: "template_for_all_singular",
      title: "Template for all singular",
      status: "draft",
      sort_order: "10",
      apply_rules_config: {
        theme: {
          and: [
            0: {
              var: "singular"
            },
            1: {
              some: [
                0: {var: "all"},
                1: true
              ]
            }
          ]
        }
      },
      active_branch: "master",
      published_branch: null,
      type: "singular",
      technology: "html"
    },
    relationships: { 
      author: {
        data: {
          type: "users",
          id: "1"
        }
      }   
    }
  }
}
```

| Name |   Type  | Description | Required |
| ---- | ------- | ----------- | -------- |
| name | string | The name of template |
| title | string | The title of template | true |
| status | string | A named status of template |
| sort_order | integer | sort order of template |
| active_branch | string | Template active branch name |
| published_branch | string | Template published branch name |
| apply_rules_config | object | Template Apply Rules Config |
| type | string |  A type of template | true |
| technology | string |  A technology of template | true |
| author | object | Template author |


**POST | PUT | PATCH**: /wp-json/wp/v2/builderius-templates/{id} - Update Builderius Template

Input(include just attributes and relationships which should be updated)

```
{
  data: {
    type: "builderius-templates",
    id: "1",
    attributes: {
      name: "template_for_all_singular",
      title: "Template for all singular",
      status: "draft",
      sort_order: "10",
      apply_rules_config: {
        theme: {
          and: [
            0: {
              var: "singular"
            },
            1: {
              some: [
                0: {var: "all"},
                1: true
              ]
            }
          ]
        }
      },
      active_branch: "master",
      published_branch: null,
      type: "singular",
      technology: "html"
    },
    relationships: { 
      branches: {
        "data": [
          {
            "type": "builderius-template-branches",
            "id": "2"
          }
        ]
      },
      author: {
        data: {
          type: "users",
          id: "1"
        }
      }   
    }
  }
}
```

| Name |   Type  | Description |
| ---- | ------- | ----------- |
| name | string | The name of template
| title | string | The title of template |
| status | string | A named status of template |
| sort_order | integer | sort order of template |
| active_branch | string | Template active branch name |
| published_branch | string | Template published branch name |
| apply_rules_config | object | Template Apply Rules Config |
| branches | array | Template branches data |
| type | string |  A type of template |
| technology | string |  A technology of template |
| author | object | Template author |

Input example for sort_order update
```
{
  data: {
    type: "builderius-templates",
    id: "1",
    attributes: {
      sort_order: "10",
    }
  }
}
```
Input example for author update
```
{
  data: {
    type: "builderius-templates",
    id: "1",
    relationships: {
      author: {
        data: {
          type: "users",
          id: "2"
        }
      } 
    }
  }
}
```


**DELETE**: /wp-json/wp/v2/builderius-templates/{id} - Delete Builderius Template

Arguments

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| force | boolean | Whether to bypass trash and force deletion | true or false | false |