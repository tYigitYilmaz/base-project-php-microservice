# ORM Template and Generator


### Available methods and implementations

- [General Purpose](#general-purpose-and-principles)
- [Entity Generation From DB](#generation-from-db)
- [Available Methods](#available-methods)
- [Extra info about Service System](#available-methods)
    - [Router](#references)
    - [Inversion of Control Container](#references)

---

## General Purpose
The main idea is generate DB design from the connected one, which is already determined in .env file.

The main purpose is that generate these entity based on folder structure and Dependency Injection pattern. 

Repository/implementation and serviceInterfaces/serviceImplementations where 2 entity examples already included for demonstration purposes.

IoC container and kernel files are available under ```\Core\IoC``` directory. 

Router of the service file also available under  ```\Core\IoC``` director and all controller method invoke operations are invoked through on kernel to active construction type dependency injection.

Required load files and interface matches are specified in ```\DependencyInjection_config.json``` file, which also is renewed by each time of generation, depend on generated Entity/Repo/Implementation files from DB.

For this version all entity files are in ```\Data\Entity``` directory.

All entities extend ```\Core\Model\EntityModel``` which contains ORM methods and not only repository interfaces 
but also their implementations are automatically generated based on developed ORM structure.

All service and controller should be written and created after DB generation.

## Generate From DB
The generator functionality has been designed for automatically generate entity from designed data base.
The main core of the  ```\Data\Entity``` 

```/db-generator/some-service/api/entity/generate``` this url end point is invoke the method of generation, after setting DB connection in env. file simple call this URL with POST method.

data layer under \Data\Entity directory.

Rooter url provider:
```
    Route::group(['prefix'=>'entity'],function (){
       Route::run('generate','generator@generate', 'POST');
    }
```

#### Available Methods

- save();
    ```  
       $foo = new Entity();
       $foo->variable = 'bar';
       $foo->save();
 
- bulkInsert($arr);
      ``` $this->iSomeRepo->bulkInsert($array);```
      
- bulkInsertReturnId($arr);

     ``` $this->iSomeRepo->bulkInsert($array);```
  
- update($arr, $id);

         $foo = new Entity();
         $foo->variable1 = 'bar';
         $foo->variable2 = 'barbar';
         $foo->update($foo, $id);

- with(...$relations);
  ```  
         $this->iFooRepo->with('barEntity');
  
  class Foo extends EntityModel{
    public function barEntity(){
            return $this->belongsTo('Data\Entity\barEntity');
        }
  }
  
- find($id);
  ```
      $this->iFooRepo->find(1);
  
- where(...$parameters);
    ```  
    $this->iAdressRepo->where('a1','a2','<')
      ->where('b1','b2','<','b3','b4','like')
      ->get();
  ``` 
  
- orderBy($feild, $orderType = null);
  
- groupBy($feild);
- having($feild, $agregateFunction, $operator, $amount);
- take($amount);
- get();
- search($inWhere, $searchArg);
  ```  
       $this->iFooRepo->search('customerNo','sdf','like')->get();
  
- delete();
    ```  
       $this->iFooRepo->where('id',1)->delete();
  
 - first();
     ```  
       $this->iFooRepo->search('barId','buzzbuzz','like')->first();

[Back To The Top](#orm-template-and-generator)

---

