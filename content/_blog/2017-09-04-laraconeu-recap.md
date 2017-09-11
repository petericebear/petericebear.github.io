---
view::extends: _includes.blog_post_base
view::yields: post_body
post::title: LaraconEU recap - 2017
post::brief: My first LaraconEU was a blast. In this post I want to give you my highlights of LaraconEU 2017.
pageTitle: Laracon EU recap 2017 - 
---------------------------------------------------

Last week was LaraconEU at the meervaart theatre in Amsterdam. This year was my first attendence at a Laracon but I am hooked already. So I will definitly signup for next years Laracon. Maybe even to LaraconUS. 
I tried to keep the highlights posted on Twitter but I will be better prepared next time, because I had lots of fun doing it.

## Workshop Day

The first day of LaraconEU was about workshops. The workshop I followed was From Evan You: *Vue.js: Advanced Use Cases from the Ground Up*. Evan You is really a great teacher when it comes to bring difficult subjects back to its basics.
 
The workshop started with how the reactivity in the framework is working in depth, and how to build your own basic reactivity system with basic Javascript functionality.

In general Reactivity (https://vuejs.org/v2/guide/reactivity.html#ad) is based on object properties and convert them into getters/setters using Object.defineProperty (https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/defineProperty].
Have a look how a basic observer looks like using Object.defineProperty.

```html
<script src="./mini-observer.js"></script>
<script>
const state = {
    count: 0,
    foo: 'bar',
}

observe(state)

autorun(function update() {
    console.log('Running the update function with count: ' + state.count)
    // Render template => basically Get the states from the vars.
})

state.count+=1
state.foo = 'baz'
</script>
```

And the Javascript file with the logic for the getters and setters.

```js
// This is an extremely simplified version of the dependency tracking
// system used in Vue, Knockout, MobX and Meteor Tracker (each with different
// implementation details, of course). The goal is to better understand how
// the tracking takes place and how data becomes "reactive".

let activeUpdate = null

function observe (obj) {
  // iterate through all properties on the object
  // and convert them into getter/setters with
  // Object.defineProperty()
  Object.keys(obj).forEach(key => {
    let val = obj[key]
    const subscribers = new Set()

    Object.defineProperty(obj, key, {
      // The getter is responsible for "registering" a
      // computation that relies on this object
      get () {
        if (activeUpdate) {
          subscribers.add(activeUpdate)
        }
        console.log(key + ' (get) is: ' + val);
        return val
      },

      // The setter is responsible for triggering all
      // registered computation to execute again
      set (newValue) {
        val = newValue
        console.log(key + ' (set) is: ' + val);
        // triggering re-computation
        subscribers.forEach(job => job())
      }
    })
  })
  return obj
}

function autorun (update) {
  // wrap the raw computation function into a "job" function that registers and
  // unregisters itself as the current active job when invoked
  const wrappedUpdate = () => {
    activeUpdate = update
    update()
    activeUpdate = null
  }

  wrappedUpdate()
}
```
In overall I have learned a lot from Evan in this workhop. Translations, validations, mixins, plugins, components, rendering, shared states, routing etc. I will use this gained knowledge for writing a blog series for building a multi-language search engine based on VueJS + Laravel + Elasticsearch.

## Conference days

When we arrived at the venue it was already packed with many developers and the vibes where great. I had the chance to meet many people that I follow on Twitter. When I look back at Laracon I think it is even more about the people coming there, and not only for the talks itself. Everyone is very helpful and willing to share their point of views opn subjects. Below are some of the talks I have attended during this conference.

****
 
**From zero to multi-platform Chatbot with BotMan**  
*Marcel Pociot*  
Chatbots are really hot at this moment. Just before Laracon, Marcel released version 2 of Botman (https://botman.io) which is a frameworkagnostic bot framework. After this talk my mind was already spinning at high-speed for thinking out a conversational bot to create with this framework.

I am currently working on a bot for searching vacancies, and an integration for asking a test-drive for a vehicle.

****
 
**Denormalization With Eloquent: How, Why and When**  
*Max Brokman*  
This talk was about using a so called 'flat' table. All the data that you want to give back as a response is in a single table row. This way you do not have to make (multiple) JOINS which can be pretty slow in some use cases. Around 5 years back I had used a flat table approach which was updated every hour. The loading times for a big query went from almost 8 seconds to 1.5 seconds. Nowadays I tend to look into Elasticsearch or SOLR if I want to search for data since that brought the loading to 2 to 5 milliseconds for the same data. In some cases though a flat table is just enough to speed up your application.

****

**Bruce Lee Driven Development**  
*Jeroen van der Gulik*  
Jeroen gave a nice insight about what a person can learn. But more over, what do you want to master. Bruce Lee was the red wire through the talk. Bruce learned all the kung-fu flavours and picked out moves that was working for him. 
Ultimately to create his own kung-fu flavour. The point of view from my end about this talk, is to find some languages and toolings that work out for me, and master them well. This way you avoid the saying: "Master of many, knower of none".

****

**Code review beyond code style**  
*Hannes Van De Vreken*  
Hannes gave a great talk about giving feedback to coders about written code within teams. A code review can be done directly within Github itself, so no extra software to be bought. Every single person in a team can do a code review wether your a senior or a junior developer. When a code review is done evry one in the team can merge the code in the codebase. The key point is that the one that merges is the one responsible for fixing it when something breaks. Always be nice when giving feedback. Keep the conversation open, because you never know what people thought at the time of writing it. 

****

**State of Laravel**  
*Taylor Otwell*  
The master at work here. Live coding out a talk and showing a lot new functions in Laravel 5.5. Some features that saved me already a lot of time are for example API Resources and Auto-Loading packages. One of the best releases yet. Going to look this talk back when it is online on YouTube. 

****

**Project Triage: What to Do When Everything Hits the Fan**  
*Eryn O'Neil*  
After a night out in the city with other Laracon attendees - this was the right wake-up-call for me. It just felt like Eryn was poking at the right points and it was painfully recognizable for some projects I have seen. When the video's are live of Laracon EU you should definitely watch this one. 

****

**Build an Airbnb-like search with Laravel, Algolia and Vue.js**  
*Julien Bourdeau*  
Algolia and VueJS, what a powerful combination. For most projects I still reach out to Elasticsearch or SOLR for searching through data. The main reason is that I am a partner of a hosting company so setting up a high-availbale production cluster is a no-brainer for me. If you have many documents to search through the pricing can be a thing with Algolia.
However the integration with Algolia in Laravel is just fenomonal  

****

**Debugging Design: 5 simple design principles to make your UI "not look terrible".**  
*Laura Elizabeth*  
As a developer who loves to code, designing is not my thing. However, after this talk I am looking at my older projects to make some style adjustments. Laura is a great speaker and can gave some good tips of toolings and resources that can be used:

- type-scale.com
- paletton.com
- Google Fonts
- designacademy.io
- debuggingdesign.co

I have signed up at designacademy.io and received very valuable tips in my mailbox. Laura is writing a book about Debugging Design which you can sign up for on the last link.

****

**Building your API with Apiary & Dredd**  
*Dries Vints*  
This talk did not go in-depth about Dredd and Apiary but just enough to tease me for looking into it further. After a week of working with these tools, I have build a solid blueprint for a new project which is used for aswell the backend as the frontend developer to work towards each other. The fun fact is that all developers work remotely and not working at the same company. The blueprint gave a very great single point-of-truth in the project which was completed within 2 weeks time.
So I recommend looking into these subjects and maybe it can also work for you. 

****

**Inside Vue Components**  
*Evan You*  
After following the workshop, this was like an overview of what we had learned from it and even beyond. Evan is a great speaker but even more a great teacher. 
I have yet so much to learn more from the VueJS framework, and Evan gave me a good starting point where to look how to master it all.


## Conclusion

Since this was my first Laracon, as well as most others at this years Laracon, I can look back to a really great experience with meeting lots of nice people. 
I have made new friends and finally met some people in real-life. 
My head is still spinning at high-speed thinking out some new ideas for a blog-series and also building some new open-source packages for the new Laravel 5.5.

Thank you Laracon!

See you next time,  
Peter - 'The Tweeter'.
