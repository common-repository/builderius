**GET**: /wp-json/wp/v2/builderius-template-commits - Get Builderius Template Commits

Output
```
[
  0: {
    data: {
      type: "builderius-template-commits",
      id: "1",
      attributes: {
        name: "u9cf96d85",
        created_at: "2020-11-04T14:23:13",
        description: "Initial commit",
        content_config: {},
      },
      relationships: { 
        branch: {
          data: {
            type: "builderius-template-branches",
            id: 1
          }
        },
        author: {
          data: {
            type: "users",
            id: 1
          }
        },  
      }
    }
  },
  1: {
    data: {
      type: "builderius-template-commits",
      id: "1",
      attributes: {
        name: "u9cf96d86",
        created_at: "2020-11-04T14:23:13",
        description: "Second commit",
        content_config: {},
      },
      relationships: { 
        branch: {
          data: {
            type: "builderius-template-branches",
            id: 1
          }
        },
        author: {
          data: {
            type: "users",
            id: 2
          }
        },  
      }
    }
  }
]
```

| Name |   Type  | Description |
| ---- | ------- | ----------- |
|  id  | integer | Unique identifier for the commit |
| created_at | string  | The date the commit was created, in the site's timezone |
| name | string | The name of commit
| description | string | Commit description |
| content_config | object | Content config |
| branch | object | Data about commit branch (by default will be included just id, but if in request will be used `include=branch` filter - will be provided all information ) |
| author | object | Data about commit author (by default will be included just id, but if in request will be used `include=author` filter - will be provided all information ) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| filter\[id] or filter\[id]\[eq] | integer | Limit result set to specific IDs |
| filter\[id]\[neq] | integer | Ensure result set excludes specific IDs |
| filter\[name] or filter\[name]\[eq] | string | Limit result set to specific names |
| filter\[name]\[neq] | string | Ensure result set excludes specific names |
| filter\[author] or filter\[author]\[eq] | integer | Limit result set to commits created by specific authors |
| filter\[author]\[neq] | integer | Ensure result set excludes commits created by specific authors |
| filter\[branch] or filter\[branch]\[eq] | integer | Limit result set to commits which belong to branch |
| filter\[branch]\[neq] | integer | Ensure result set excludes commits which belong to branch |
| page\[number] | integer | Current page of the collection | | 1 |
| page\[size] | integer | Maximum number of items to be returned in result set | 1...100 | 10 |
| include | string | A list of output fields which are not included by default | author, branch |

Output example for request `/wp-json/wp/v2/builderius-template-branches?filter[author]=1&include=branch`
```
[
  0: {
    data: {
      type: "builderius-template-branches",
      id: "1",
      attributes: {
        name: "u9cf96d85",
        created_at: "2020-11-04T14:23:13",
        description: "Initial commit",
        content_config: {},
      },
      relationships: { 
        branch: {
          data: {
            type: "builderius-template-branches",
            id: 1
          }
        },
        author: {
          data: {
            type: "users",
            id: 1
          }
        },  
      }
    },
    included: [
      0: {
        type: "builderius-template-branches",
        id: 1
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
          commits: {
            data: [
              {
                type: "builderius-template-commits",
                id: 1
              },
              {
                type: "builderius-template-commits",
                id: 2
              }
            ]
          }
        }
      }
    ]
  }
]
```


**GET**: /wp-json/wp/v2/builderius-template-commits/{id} - Get Builderius Template Commit

Output

```
{
  data: {
    type: "builderius-template-commits",
    id: "1",
    attributes: {
      name: "u9cf96d85",
      created_at: "2020-11-04T14:23:13",
      description: "Initial commit",
      content_config: {},
    },
    relationships: { 
      branch: {
        data: {
          type: "builderius-template-branches",
          id: 1
        }
      },
      author: {
        data: {
          type: "users",
          id: 1
        }
      },  
    }
  }
}
```

| Name |   Type  | Description |
| ---- | ------- | ----------- |
|  id  | integer | Unique identifier for the commit |
| created_at | string  | The date the commit was created, in the site's timezone |
| name | string | The name of commit
| description | string | Commit description |
| content_config | object | Content config |
| branch | object | Data about commit branch (by default will be included just id, but if in request will be used `include=branch` filter - will be provided all information ) |
| author | object | Data about commit author (by default will be included just id, but if in request will be used `include=author` filter - will be provided all information ) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| include | string | A list of output fields which are not included by default | author, branch |


**POST**: /wp-json/wp/v2/builderius-template-commits - Create Builderius Template Commit

Input

```
{
  data: {
    type: "builderius-template-commits",
    id: "1",
    attributes: {
      name: "u9cf96d85",
      description: "Initial commit",
      content_config: {},
    },
    relationships: { 
      branch: {
        data: {
          type: "builderius-template-branches",
          id: 1
        }
      },
      author: {
        data: {
          type: "users",
          id: 1
        }
      },  
    }
  }
}
```

| Name |   Type  | Description | Required |
| ---- | ------- | ----------- | -------- |
| name | string | The name of commit|
| description | string | Commit description | true |
| content_config | object | Content config | true |
| branch | object | Data about commit branch | true |
| author | object | Data about commit author |


**DELETE**: /wp-json/wp/v2/builderius-template-commits/{id} - Delete Builderius Template Commit.

Commit can be deleted only if it is not active_commit or published_commit for branch

Arguments

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| force | boolean | Whether to bypass trash and force deletion | true or false | false |