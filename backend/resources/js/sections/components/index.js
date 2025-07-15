
export default function (Alpine) {

  const components = [];
  const component = import.meta.glob('./**/*.js', { eager: true })
  const componentFilesEntries = Object.entries(component);
  for (const [componentPath, moduleImport] of componentFilesEntries) {
    // console.log(componentPath)
    const componentName = componentPath
      .split("./")
      .pop()
      ?.replace(".js", "")
      ?.replace("/index", "")
  
    if (!componentName) {
      console.warn(
        `The componentName couldn't be extracted from path > ${componentPath} `
      )
      continue
    }

    window[componentName] = moduleImport.default;
    components[componentName] = moduleImport;

    // console.log(window[componentName])
  }

//   console.log(components)
  let comp = {
    get(component){
      component = component.replace('.', '/');
      component = components[component];

      if(!component) {
        console.warn(
          `The componentName couldn't be extracted from path > ${component} `
        )
        // return;
      }

      // console.log(component.default)
      return component.default();
    }
  };


//   const state = Alpine.reactive({
//     mode: 'web',
//     base: '',
//     href: location.href,
//     path: '',
//     query: {},
//     params: {},
//     loading: false
//   })

//   const templateCaches = {}
//   const inLoadProgress = {}
//   const inMakeProgress = new Set()

}