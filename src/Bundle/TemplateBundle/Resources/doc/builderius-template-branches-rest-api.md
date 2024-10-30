**GET**: /wp-json/wp/v2/builderius-template-branches - Get Builderius Template Branches

Output
```
[
  0: {
    data: {
      type: "builderius-template-branches",
      id: "1",
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
      type: "builderius-template-branches",
      id: "2",
      attributes: {
         name: "dev",
         created_at: "2020-11-04T14:23:13",
         updated_at: "2020-11-10T14:22:06",
         base_branch: null,
         base_commit: null,
         active_commit: "u9cf96d86",
         published_commit: null,
         not_committed_config: null
      },
      relationships: { 
        template: {
          data: {
            type: "builderius-templates",
            id: 2
          }
        },
        commits: {
          data: [
            0: {
              type: "builderius-template-commits"
              id: 2
            }
          ]
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
]
```

| Name |   Type  | Description |
| ---- | ------- | ----------- |
|  id  | integer | Unique identifier for the branch |
| created_at | string  | The date the branch was created, in the site's timezone |
| updated_at | string | The date the branch was last modified, in the site's timezone
| name | string | The name of branch
| base_branch | string | Branch name which is base for current branch |
| base_commit | string | Commit name which is base for current branch |
| active_commit | string | Active commit name |
| published_commit | string | Published commit name |
| not_committed_config | object | Saved(not committed) config |
| commits | array | Branch commits data (by default will be included just id, but if in request will be used `include=commits` filter - will be provided all information ) |
| template | object | Data about branch template (by default will be included just id, but if in request will be used `include=template` filter - will be provided all information ) |
| author | object | Data about branch author (by default will be included just id, but if in request will be used `include=author` filter - will be provided all information ) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| filter\[id] or filter\[id]\[eq] | integer | Limit result set to specific IDs |
| filter\[id]\[neq] | integer | Ensure result set excludes specific IDs |
| filter\[name] or filter\[name]\[eq] | string | Limit result set to specific names |
| filter\[name]\[neq] | string | Ensure result set excludes specific names |
| filter\[author] or filter\[author]\[eq] | integer | Limit result set to branches created by specific authors |
| filter\[author]\[neq] | integer | Ensure result set excludes branches created by specific authors |
| filter\[template] or filter\[template]\[eq] | integer | Limit result set to branches which belong to template |
| filter\[template]\[neq] | integer | Ensure result set excludes branches which belong to template |
| page\[number] | integer | Current page of the collection | | 1 |
| page\[size] | integer | Maximum number of items to be returned in result set | 1...100 | 10 |
| include | string | A list of output fields which are not included by default | commits, author, template |

Output example for request `/wp-json/wp/v2/builderius-template-branches?filter[template]=1&include=commits`
```
[
  0: {
    data: {
      type: "builderius-template-branches",
      id: "1",
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
        type: "builderius-template-commits",
        id: 1
        attributes: {
          name: "u9cf96d85",
          created_at: "2020-11-04T14:23:13",
          description: "Initial commit",
          content_config: {},
        },
        relationships: {
          author: {
            data: {
              type: "users",
              id: 1
            }
          },
          branch: {
            data: {
              type: "builderius-template-branches",
              id: 1
            }
          }
        }
      }
    ]
  }
]
```


**GET**: /wp-json/wp/v2/builderius-template-branches/{id} - Get Builderius Template Branch

Output

```
{
  data: {
    type: "builderius-template-branches",
    id: "1",
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
|  id  | integer | Unique identifier for the branch |
| created_at | string  | The date the branch was created, in the site's timezone |
| updated_at | string | The date the branch was last modified, in the site's timezone
| name | string | The name of branch
| base_branch | string | Branch name which is base for current branch |
| base_commit | string | Commit name which is base for current branch |
| active_commit | string | Active commit name |
| published_commit | string | Published commit name |
| not_committed_config | object | Saved(not committed) config |
| commits | array | Branch commits data (by default will be included just id, but if in request will be used `include=commits` filter - will be provided all information ) |
| template | object | Data about branch template (by default will be included just id, but if in request will be used `include=template` filter - will be provided all information ) |
| author | object | Data about branch author (by default will be included just id, but if in request will be used `include=author` filter - will be provided all information ) |

Filters

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| include | string | A list of output fields which are not included by default | commits, author, template |

**POST**: /wp-json/wp/v2/builderius-template-branches - Create Builderius Template Branch

Input

```
{
  data: {
    type: "builderius-template-branches",
    attributes: {
      name: "master",
      base_branch: null,
      base_commit: null,
      active_commit: "null",
      published_commit: null,
      not_committed_config: null
    },
    relationships: { 
      template: {
        data: {
          type: "builderius-templates",
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
| name | string | The name of template | true |
| base_branch | string | Branch name which is base for current branch |
| base_commit | string | Commit name which is base for current branch |
| active_commit | string | Active commit name |
| published_commit | string | Published commit name |
| not_committed_config | object | Saved(not committed) config |
| template | object | Data about branch template | true |
| author | object | Data about branch author |


**POST | PUT | PATCH**: /wp-json/wp/v2/builderius-template-branches/{id} - Update Builderius Template Branch

Input(include just attributes and relationships which should be updated)

```
{
  data: {
    type: "builderius-template-branches",
    id: "1",
    attributes: {
      name: "master",
      base_branch: null,
      base_commit: null,
      active_commit: "u9cf96d85",
      published_commit: null,
      not_committed_config: null
    },
    relationships: { 
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
| name | string | The name of template | true |
| base_branch | string | Branch name which is base for current branch |
| base_commit | string | Commit name which is base for current branch |
| active_commit | string | Active commit name |
| published_commit | string | Published commit name |
| not_committed_config | object | Saved(not committed) config |
| template | object | Data about branch template | true |
| author | object | Data about branch author |

Input example for active_commit update
```
{
  data: {
    type: "builderius-template-branches",
    id: "1",
    attributes: {
      active_commit: "wqxws2w2w1",
    }
  }
}
```
Input example for author update
```
{
  data: {
    type: "builderius-template-branches",
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


**DELETE**: /wp-json/wp/v2/builderius-template-branches/{id} - Delete Builderius Template Branch

Branch can be deleted only if it is not active_branch or published_branch for template


Arguments

| Name |   Type  | Description | Possible Values | Default Value |
| ---- | ------- | ----------- | --------------- | ------------- |
| force | boolean | Whether to bypass trash and force deletion | true or false | false |