/**
 * Created by salt on 10.10.2016.
 */
$(document).ready(function() {
    "use strict";





















    function importJson(jsonString) {
        var obj = JSON.parse(jsonString),
            data = [];

        obj.forEach(function(item, index) {
            data.push({
                id          : item.id,
                parent      : item.parent,
                text        : item.label,
                //icon        : "string", // string for custom
                state       : {
                    opened    : true,  // is the node open
                    disabled  : false,  // is the node disabled
                    selected  : false,  // is the node selected
                },
                li_attr     : {},  // attributes for the generated LI node
                a_attr      : {}  // attributes for the generated A node
            });
        });
        return data;
    }
    function init() {
        var data = importJson(window.tempFilesForFileBrowser),
            treeConf = {
                'core' : {
                    'data' : data
                }
            };

        $('#filebrowser>.file-tree').jstree(treeConf);
    }
    init();



    return;
    /**
     * @param url
     * @returns {string} parent path  without trailing slashs'
     */
    function getParentPath(path) {
        if (path === '/') return path;
        path = path.replace(/\/$/, '');
        path = path.substring(0, path.lastIndexOf("/")+1);
        if (path != '/') {
            path = path.replace(/\/$/, '');
        }
        return path;
    }
    /**
     * Base Node Class
     * @param conf
     * @constructor
     */
    function Node(conf) {
        if(conf.type === "folder") return new Folder(conf);
        this.id = conf.id || null;
        this.path = conf.path;
        this.name = conf.name || "undefined";
        this.type = conf.type || null;
        this.parent;
    }
    /**
     * Class File
     * @param conf
     * @constructor
     */
    function File(conf) {
        if(conf.type === "folder") return new Folder(conf);
        this.id = conf.id || null;
        this.path = conf.path;
        this.name = conf.name || "undefined";
        this.type = conf.type || null;
    }
    File.prototype = Object.create(Node.prototype);
    /**
     * Class Folder
     * @param conf
     * @constructor
     */
    function Folder(conf) {
        if(conf.type !== "folder") return new File(conf);
        this.id = conf.id || null;
        this.path = conf.path;
        this.name = conf.name || "undefined";
        this.type = "folder";
        this.files = [];
        this.folders = [];
        this.$ele = $('<li></li>');
        this.$ele.html('<div class="name">'+ this.name +'</div><ul class="children"></ul>');
    }
    Folder.prototype = Object.create(Node.prototype);
    /**
     * add a child Node to this
     * @param {Node} node
     */
    Folder.prototype.add = function(node) {
        if (node instanceof Folder) {
            node.parent = this;
            node.$ele.detach();
            this.folders.push(node);
            $('>.children', this.$ele).append(node.$ele);
        } else if (node instanceof File) {
            node.parent = this;
            this.files.push(node);
        }
    };
    Folder.prototype.getByPath = function(path) {
        if (this.path === path) return this;
        //search in child files
        for(var i = 0; i < this.files.length; i++) {
            if (this.files[i].path === path) return this.files[i];
        }
        //search in child folders
        for(var i = 0; i < this.folders.length; i++) {
            var result = null;
            result = this.folders[i].getByPath(path);
            if (result) {
                return result;
            }
        }
        return false;
    };
    Folder.prototype.get = function(path) {

    };
    function iterateFolders(fn) {
        var rec = function(node) {
            fn.call(node);
            for(var i = 0; i < node.folders.length; i++) {
                rec(node.folders[i]);
            }
        };
        rec(dataTree);
    }


    var fragment = document.createDocumentFragment();
    var dataTree = new Folder({
        id: 0,
        path: '/',
        name: 'root',
        type: 'folder'
    });
    //dataTree.$ele = $('<div class="file-tree"></div>');
    //dataTree.$ele.html('<ul class="children"></ul>');
    //$('#filebrowser').append(dataTree.$ele);
    function updateTree(node) {
        var data = [];
        iterateFolders(function() {
            console.log(this);
            data.push({
                'text' : this.name,
                //'state' : {
                //    'opened' : true,
                //    'selected' : true
                //},
                'children' : []
            });
        });
    }
});
