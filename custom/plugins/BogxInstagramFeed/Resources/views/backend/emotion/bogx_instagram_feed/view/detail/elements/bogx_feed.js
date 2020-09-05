//
// {namespace name="backend/emotion/bogx_instagram_feed"}
//{block name="backend/emotion/view/detail/elements/base"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.Emotion.view.detail.elements.BogxFeed', {

    /**
     * Extend from the base class for the grid elements.
     */
    extend: 'Shopware.apps.Emotion.view.detail.elements.Base',

    /**
     * Create the alias matching with the xtype you defined for your element.
     * The pattern is always 'widget.detail-element-' + xtype
     */
    alias: 'widget.detail-element-emotion-components-bogxfeed',

    /**
     * Contains the translations of each input field which was created with the EmotionComponentInstaller.
     * Use the name of the field as identifier
     */
    snippets: {
        'username': {
            'fieldLabel': '{s name=usernameFieldLabel}{/s}',
            'supportText': '{s name=usernameSupportText}{/s}',
            'helpText': '{s name=usernameHelpText}{/s}'
        }
    },

    /**
     * You can define an additional CSS class which will be used for the grid element.
     */
    componentCls: 'emotion--bogx-feed',

    /**
     * Define the path to an image for the icon of your element.
     * You could also use a base64 string.
     */
    icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAK6wAACusBgosNWgAAABZ0RVh0Q3JlYXRpb24gVGltZQAwOC8zMS8xN1VREFAAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzVxteM2AAACx0lEQVRIicXXS4iWZRQH8N/5nAIXo1Bouohy1KiIgpouWlFEYKuolRUpBS6qXVHrahO0NaIWQpgRZQQtWwRBUI4RXRcTabeNXSwItKYh87R4z+d8837fNzPMrQMP73P58/yfc3nOc97ITG2JiFHcgLtxIc4gEX3g2ZLoVPsJ72IiM//qR2bOargfH2C6NlpKm8b72N3H00PYwUH8vQyE7TaFA1jT5YuuqSPiDdyL8+ukh/EKvusx4ULkLNbgcuzFbo2LpvFWZu45Z2o8gj+L4ARuaZtmsQ23155ZHHu7yl6Az+qkU7hpGcjGy7QP13inJkDPYgLr4CEzgfQ8RpdIemUpkvgVD5apX+rx9z0juK3Hf+9k5qm5HBgRF2MHxkqDYziamScKsg5bqr8W2zMzI+LtcukIdsER/Is/sGUOTS6pU5/WH7WnsB8bC/tY7Xe4a0FcXdh/cAi+rJOfxNgQ0h2YHEDYbl8NixFc20N8sFOkyg99mSkixvCi5nrA13gC12uC6Ckcr7WrsD8iNg921Gz5vMh/w9YBJ321R6PXsGEAZhPe7MEdWIjGQyUirimt4D08nZkn27jM/BnP4KOaujEits2193zZaBwbq38oM78dBszMSbxew4tw81KIt2tepyn8MA8WvqnvWowuhXjFZD7iY/hdo8GlC9jvsvpOae72ook/0aQ92BMRW4cBI+IKPFDDX/Dhookz84sihzvxbERsGEC6SRPVO2vqaGYeb+PaMt89HsOnZu7oJB7XRPx1eFLjku76x9g83z3mf0yZ3QLgvPoOMvkR3IWXC9+W03gBuzJzYohlz/T0OyOa3DuO9ZrU9/0Q8h/xaEQ8Z+5nsU8iomMmEXUPuryFwBAzr9cqBFiB0mcA8a3apU8trEixp3lm7zCg2Fup8jawDftwX833l7dFvqoF/SDzrMovzDlT98pq/LT9B2VlhUNN2rTyAAAAAElFTkSuQmCC'
    //icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAYCAYAAACSuF9OAAAC5UlEQVRIia2WTUhVQRTHf09UKhGsVWFQQVLiQqSgTdCHqRUkgdsGW82qD22yVmXgymjCstVsRMZ1QkFaZCK0aSGVoLkIREprlYFUgiEt3rm98fk+7nu8P1zu3DPn/M//zpz5SFBCWOcvA0PyqYxWI4VylJVSEHAqaL8uhqDUgprlPWe0+l4MQWxB1vlK63xTjv5DQK18Tgb2Jut8ZUkFWefPArNAZw6300E7nK5OYFY48qI8j5ADwADQHoMrmq4NYCqt7yAwZp1/BnQZrRYKEmSd3w7clmdb0PUli38ZqYJ+b7RayRLTDrRa5/uBfqPVn3SuLVNmnb8IzAG9gZhPQIvRymYSBDQCu6S9aXVJTItwIJy9wJzkyizIOl9nnR8HRoH9Yl4FbgKNRqtcy7g5aL9J75TYRqBHOJEco9b5cet8XeSbsM5XAXeAG0BFwDMC3DJafcshJPqZF8A5YB2oMVr9zuG7B7gPXArM68BDoC9hnf9KarkCfACuGq3e5hMiCSqAFaAKmDJanYwZdxx4QnLkIiyVBWJWgCvA0bhiBMdEDAT7Tz5IjiOSM1oEtWFRJ9LecRHWz0SBsYngAZJFvSztGpJDOC3DGRfRcv8FvIsbJDmmgUHJDbCcsM5XkyzqLgosauv8DuCnxI0Zrc7HEJKtqAeAvkTgeBh4BLQGjqvAPeCx0epvBvJW4KV89hitHuQQUg5cE77qoOsVcN1oNQ/BPmS0mjdatQEdwKKYqwELzFjnw7MqQqz6kdgZ4YrELAIdRqu2SMwmQYGwp0A90AesibkemLDOd2cR9AP4mEVMt4itF9OacNdLrk3IeJbJGXPXOj9EchovSNe+INFOILqOTBqtNjJxhTHAc5LTU9jhGghbANrl6jCY1n2C1AjnW+6fSW6243n84t2HhKgBGA7MZ4L2lvMrwDDQEEcMFL4J/od1fo5kXSwZrfYWy5OOou7U1vndpIq00N05J4q95MedroLxD0lu8mWVpxlSAAAAAElFTkSuQmCC'

    /**
     * You can override the original `createPreview()` method
     * to create a custom grid preview for your element.
     *
     * @returns { string }
     */	 
});
//{/block}