# laravel_dingbot

我只想要一个简单的机器人！！

最近事情有点多，精力有限，稍后完善！


## Usage

```
    Bot::text([
        //balabala.
    ]);

    //选择机器人
    Bot::select('tianmao')->text([
        //balabala.
    ]);

    //@at
    Bot::at('139..', '138...')->text([
        //balabala
    ])

    //or

    Bot::at(['139..','138...'])->text([
        //balabala
    ]);

    //or 
    Bot::atAll()->text([
        //balabala
    ]);
```