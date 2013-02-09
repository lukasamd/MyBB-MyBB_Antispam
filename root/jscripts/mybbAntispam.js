/**
 * This file is part of MyBB Antispam plugin for MyBB.
 * Copyright (C) 2010-2013 Lukasz Tkacz <lukasamd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */ 
 

var mybbAntispam = {
    refresh: function()
    {
        var imagehash = $('imagehash').value;
        this.spinner = new ActivityIndicator("body", {image: imagepath + "/spinner_big.gif"});
        new Ajax.Request('xmlhttp.php?action=refreshMyBBAntispam&imagehash='+imagehash, {
        	method: 'get',
        	onComplete: function(request) { mybbAntispam.refresh_complete(request); }
        });
        return false;
    },

    refresh_complete: function(request)
    {
        var oldHash = $('specialHash').value;
        $(oldHash).value = '';
        $(oldHash).className = 'textbox';
        
        if(request.responseText.match(/<error>(.*)<\/error>/))
        {
            message = request.responseText.match(/<error>(.*)<\/error>/);
            
            if(!message[1])
            {
                message[1] = "An unknown error occurred.";
            }
            
            alert('There was an error fetching the new captcha.\n\n'+message[1]);
        }
        else if(request.responseText)
        {
            var mybbAntispamData = request.responseText.split("|");
            
            // Delete old validator event and register a new one
            Event.stopObserving(oldHash);
            $(oldHash).insert({after: "<script type='text/javascript'>" + mybbAntispamData[4] + "</script>"});
            
            // Change old captcha field attributes to new
            $(oldHash).setAttribute("name", mybbAntispamData[1]);
            $(oldHash).setAttribute("size", mybbAntispamData[2]);
            $(oldHash).setAttribute("id", mybbAntispamData[1]);
            $('specialHash').value = mybbAntispamData[1];
            
            // Change captcha img
            $('mybbAntispam_img').src = "captcha.php?action=regimage&imagehash=" + mybbAntispamData[3];
            $('imagehash').value = mybbAntispamData[3];
        }
        
        if(this.spinner)
        {
        	this.spinner.destroy();
        	this.spinner = '';
        }
        
        // Delete old hash info-status div
        $(oldHash + '_status').remove();
	}
};