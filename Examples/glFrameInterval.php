<?php
// Include WordPress
define('WP_USE_THEMES', false);
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include "$root/new/wp-load.php";
get_header();
?>

<link rel="stylesheet" href="/css/c4tutorial.css" type="text/css" />
<link rel="stylesheet" href="/css/pygments.css" type="text/css" />


<script type="text/javascript">
/*<![CDATA[*/
var asciidoc = {  // Namespace.

/////////////////////////////////////////////////////////////////////
// Table Of Contents generator
/////////////////////////////////////////////////////////////////////

/* Author: Mihai Bazon, September 2002
 * http://students.infoiasi.ro/~mishoo
 *
 * Table Of Content generator
 * Version: 0.4
 *
 * Feel free to use this script under the terms of the GNU General Public
 * License, as long as you do not remove or alter this notice.
 */

 /* modified by Troy D. Hanson, September 2006. License: GPL */
 /* modified by Stuart Rackham, 2006, 2009. License: GPL */

// toclevels = 1..4.
toc: function (toclevels) {

  function getText(el) {
    var text = "";
    for (var i = el.firstChild; i != null; i = i.nextSibling) {
      if (i.nodeType == 3 /* Node.TEXT_NODE */) // IE doesn't speak constants.
        text += i.data;
      else if (i.firstChild != null)
        text += getText(i);
    }
    return text;
  }

  function TocEntry(el, text, toclevel) {
    this.element = el;
    this.text = text;
    this.toclevel = toclevel;
  }

  function tocEntries(el, toclevels) {
    var result = new Array;
    var re = new RegExp('[hH]([1-'+(toclevels+1)+'])');
    // Function that scans the DOM tree for header elements (the DOM2
    // nodeIterator API would be a better technique but not supported by all
    // browsers).
    var iterate = function (el) {
      for (var i = el.firstChild; i != null; i = i.nextSibling) {
        if (i.nodeType == 1 /* Node.ELEMENT_NODE */) {
          var mo = re.exec(i.tagName);
          if (mo && (i.getAttribute("class") || i.getAttribute("className")) != "float") {
            result[result.length] = new TocEntry(i, getText(i), mo[1]-1);
          }
          iterate(i);
        }
      }
    }
    iterate(el);
    return result;
  }

  var toc = document.getElementById("toc");
  if (!toc) {
    return;
  }

  // Delete existing TOC entries in case we're reloading the TOC.
  var tocEntriesToRemove = [];
  var i;
  for (i = 0; i < toc.childNodes.length; i++) {
    var entry = toc.childNodes[i];
    if (entry.nodeName.toLowerCase() == 'div'
     && entry.getAttribute("class")
     && entry.getAttribute("class").match(/^toclevel/))
      tocEntriesToRemove.push(entry);
  }
  for (i = 0; i < tocEntriesToRemove.length; i++) {
    toc.removeChild(tocEntriesToRemove[i]);
  }

  // Rebuild TOC entries.
  var entries = tocEntries(document.getElementById("content"), toclevels);
  for (var i = 0; i < entries.length; ++i) {
    var entry = entries[i];
    if (entry.element.id == "")
      entry.element.id = "_toc_" + i;
    var a = document.createElement("a");
    a.href = "#" + entry.element.id;
    a.appendChild(document.createTextNode(entry.text));
    var div = document.createElement("div");
    div.appendChild(a);
    div.className = "toclevel" + entry.toclevel;
    toc.appendChild(div);
  }
  if (entries.length == 0)
    toc.parentNode.removeChild(toc);
},


/////////////////////////////////////////////////////////////////////
// Footnotes generator
/////////////////////////////////////////////////////////////////////

/* Based on footnote generation code from:
 * http://www.brandspankingnew.net/archive/2005/07/format_footnote.html
 */

footnotes: function () {
  // Delete existing footnote entries in case we're reloading the footnodes.
  var i;
  var noteholder = document.getElementById("footnotes");
  if (!noteholder) {
    return;
  }
  var entriesToRemove = [];
  for (i = 0; i < noteholder.childNodes.length; i++) {
    var entry = noteholder.childNodes[i];
    if (entry.nodeName.toLowerCase() == 'div' && entry.getAttribute("class") == "footnote")
      entriesToRemove.push(entry);
  }
  for (i = 0; i < entriesToRemove.length; i++) {
    noteholder.removeChild(entriesToRemove[i]);
  }

  // Rebuild footnote entries.
  var cont = document.getElementById("content");
  var spans = cont.getElementsByTagName("span");
  var refs = {};
  var n = 0;
  for (i=0; i<spans.length; i++) {
    if (spans[i].className == "footnote") {
      n++;
      var note = spans[i].getAttribute("data-note");
      if (!note) {
        // Use [\s\S] in place of . so multi-line matches work.
        // Because JavaScript has no s (dotall) regex flag.
        note = spans[i].innerHTML.match(/\s*\[([\s\S]*)]\s*/)[1];
        spans[i].innerHTML =
          "[<a id='_footnoteref_" + n + "' href='#_footnote_" + n +
          "' title='View footnote' class='footnote'>" + n + "</a>]";
        spans[i].setAttribute("data-note", note);
      }
      noteholder.innerHTML +=
        "<div class='footnote' id='_footnote_" + n + "'>" +
        "<a href='#_footnoteref_" + n + "' title='Return to text'>" +
        n + "</a>. " + note + "</div>";
      var id =spans[i].getAttribute("id");
      if (id != null) refs["#"+id] = n;
    }
  }
  if (n == 0)
    noteholder.parentNode.removeChild(noteholder);
  else {
    // Process footnoterefs.
    for (i=0; i<spans.length; i++) {
      if (spans[i].className == "footnoteref") {
        var href = spans[i].getElementsByTagName("a")[0].getAttribute("href");
        href = href.match(/#.*/)[0];  // Because IE return full URL.
        n = refs[href];
        spans[i].innerHTML =
          "[<a href='#_footnote_" + n +
          "' title='View footnote' class='footnote'>" + n + "</a>]";
      }
    }
  }
},

install: function(toclevels) {
  var timerId;

  function reinstall() {
    asciidoc.footnotes();
    if (toclevels) {
      asciidoc.toc(toclevels);
    }
  }

  function reinstallAndRemoveTimer() {
    clearInterval(timerId);
    reinstall();
  }

  timerId = setInterval(reinstall, 500);
  if (document.addEventListener)
    document.addEventListener("DOMContentLoaded", reinstallAndRemoveTimer, false);
  else
    window.onload = reinstallAndRemoveTimer;
}

}
asciidoc.install(2);
/*]]>*/
</script>
</head>
<div class="row">
<div id="header" class="span8">

<h2>GL: Frame Interval</h2>
<span id="author">Written by: <a href="mailto:examples@c4ios.com">Travis Kirton</a></span>
</div>

<div class="span3 offset1 calls">
<a href="https://gist.github.com/3231904" title="Get the code from Github"><button class="btn-download"><span>{ }</span></button></a>
</div>
</div>


<div class="row">
  <div id="content" class="span9">
<div class="flex-video widescreen vimeo">
<iframe src="http://player.vimeo.com/video/48915439" width="900" height="506" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>
<div id="preamble">
<div class="sectionbody">
<div class="paragraph"><p>You can easily control the rate at which a GL object animates.</p></div>
</div>
</div>
<div class="sect1">
<h2 id="_frame_interval">1. Frame Interval</h2>
<div class="sectionbody">
<div class="paragraph"><p>Every C4GL object has a <tt>frameInterval</tt> property that you can change.</p></div>
<div class="paragraph"><p>The system on iOS uses a <em>frame interval</em> approach for controlling the framerate of OpenGL objects. Instead of specifying <tt>gl.rate = 0.5f;</tt> for a half-speed playback (like you would with C4Movie), you have to specify the rate in terms of <em>frames</em>.</p></div>
<div class="paragraph"><p>By default, a GL object will try to match 60 frames per second (FPS) when it is animating. Also, it&#8217;s default animation frame interval is 1, meaning that for every GL frame that will, or should, occur the object will redraw.</p></div>
<div class="paragraph"><p>If you want a half-speed animation then you technically want your GL object to animate <em>every other frame</em>. In terms of frame intervals, this means 2.</p></div>
<div class="sect2">
<h3 id="_interval_equation">1.1. Interval Equation</h3>
<div class="paragraph"><p>The interval equation looks something like this:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">rate</span> <span class="o">=</span> <span class="n">interval</span><span class="o">/</span><span class="n">defaultFPS</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>Because the default frame rate is 60, this translates to&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">rate</span> <span class="o">=</span> <span class="n">interval</span><span class="o">/</span><span class="mi">60</span><span class="p">;</span>
</pre></div></div></div>
<div class="paragraph"><p>So, the default animation rate of a GL object is&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">rate</span> <span class="o">=</span> <span class="mi">1</span><span class="o">/</span><span class="mi">60</span><span class="p">;</span>
</pre></div></div></div>
<div class="olist lowerroman"><ol class="lowerroman">
<li>
<p>
or 1/60th of a second.
</p>
</li>
</ol></div>
</div>
<div class="sect2">
<h3 id="_a_new_interval">1.2. A New Interval</h3>
<div class="paragraph"><p>Here&#8217;s a few examples of setting the interval to help understand the nonsense above&#8230;</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">rate</span> <span class="o">=</span> <span class="mf">1.0f</span><span class="p">;</span>  <span class="c1">// 1/60th of a second, 60fps</span>
<span class="n">rate</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>  <span class="c1">// 2/60th = 1/30th of a second, 30fps</span>
<span class="n">rate</span> <span class="o">=</span> <span class="mf">15.0f</span><span class="p">;</span> <span class="c1">// 15/60th = 1/4, 4fps</span>
<span class="n">rate</span> <span class="o">=</span> <span class="mf">30.0f</span><span class="p">;</span> <span class="c1">// 30/60th = 1/2, 2fps</span>
<span class="n">rate</span> <span class="o">=</span> <span class="mf">60.0f</span><span class="p">;</span> <span class="c1">// 60/60th = 1/1, 1fps</span>
<span class="n">rate</span> <span class="o">=</span> <span class="mf">120.0f</span><span class="p">;</span> <span class="c1">// 120/60th = 2, 1 frame every 2 seconds</span>
</pre></div></div></div>
<div class="paragraph"><p>etc&#8230;</p></div>
</div>
<div class="sect2">
<h3 id="_in_practice">1.3. In Practice</h3>
<div class="paragraph"><p>To actually change the <tt>frameInterval</tt> property of a GL object, do the following:</p></div>
<div class="listingblock">
<div class="content"><div class="highlight"><pre><span class="n">gl</span><span class="py">.frameInterval</span> <span class="o">=</span> <span class="mf">2.0f</span><span class="p">;</span>
</pre></div></div></div>
<div class="admonitionblock">
<table><tr>
<td class="icon">
<img src="../images/icons/note.png" alt="Note" />
</td>
<td class="content">
<div class="title">What&#8217;s Actually Going On?</div>In the video we&#8217;re showing you, we&#8217;ve mapped the left side of the screen to 1, and the right side of the screen to 60&#8230; So, wherever the screen is touched the position of the touch is translated to something between 1 and 60.</td>
</tr></table>
</div>
</div>
</div>
</div>
  </div>
  <div class="span3">
  <div id="toc">
    <div id="toctitle">Table of Contents</div>
    <noscript><p><b>JavaScript must be enabled in your browser to display the table of contents.</b></p></noscript>
  </div>
</div>
</div>
</div>

<?php get_footer(); ?>
