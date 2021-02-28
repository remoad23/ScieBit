import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})

/**
 * contains references to Object to pass them between components
 */
export class SharedService {

  // shared objects container
  shared: any;


  constructor()
  {
    this.shared = {};
  }

  /**
   *
   * @param key the value that identifies the object passed into the shared container
   * @param value the object that is passed to the shared container
   * @returns will return the identifier to get object from the container
   */
  insertObject(key: string,obj: any)
  {
    this.shared[key] = obj;
  }


  /**
   * get the object from the container
   * @param identifier the unique id for the object that is inside the container
   */
  getObject(key: string) : any
  {
    return this.shared[key] || "NotFound";
  }

  /**
   * remove property from shared object
   * @param key
   */
  removeObject(key: string)
  {
    delete this.shared[key];
  }
}
