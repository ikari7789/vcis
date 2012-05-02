<?php
$this->pageTitle='Help | '.Yii::app()->name;
$this->breadcrumbs=array(
	'Help',
);

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/help.js', CClientScript::POS_HEAD);
Yii::app()->clientScript->registerCSSFile(Yii::app()->request->baseUrl.'/css/help.css');

$this->widget('application.extensions.fancybox.EFancyBox', array(
    'target'=>'a.fancy',
    'config'=>array(),
    )
);

?>
<h1>VCIS User Help</h1>
<span class="info">Click on a field to see help documentation.</span>
<ul id="help">
	<li>
		<a href="#" id="dropPurpose"><span>+</span>Purpose of the Visual Classroom Inventory System (VCIS)</a>
		<div id="purposeStuff">  
		    <p>
		    	The purpose of the VCIS is to allow users to view classrooms that can
		    	be found on the UW Whitewater campus, and to evaluate those classrooms
		    	based on what features, furnishings, and layout each particular classroom
		    	provides. For purposes of UW Whitewater faculty and staff, the VCIS allows
		    	for the creation of a 'favorites' list of classrooms, that may be viewed,
		    	the list prioritized, and then sent via email to whomever you would like,
		    	such as the Registrar's Room Scheduler to hopefully find and assign you the
		    	room you would like if it is available.  Note, that you don't need to be
		    	faculty or staff to use the	list feature, if in case you would just like
		    	to send a list of rooms to yourself, a friend, or a prospective student
		    	looking for a university to attend. If you are someone who requires
		    	accommodations, the VCIS is also an easy way to find if the accommodations
		    	you seek are available or present in a particular classroom you may be concerned about.
		  	</p>
		  	<p>
		  		The VCIS is not used to reserve rooms on campus, if you are looking to
		  		reserve a room for an event you should visit the UW Whitewater
		  		University Center <?php echo CHtml::link('here', 'http://uc.uww.edu/', array('title'=>'link to UC')); ?>.
		    </p>  
		</div>
	</li>
	<li>
		<a href="#" id="dropHomepage"><span>+</span>Where to start</a>
		<div id="homepageStuff">
		  	<?php echo CHtml::link(
		  		CHtml::image(
		  			Yii::app()->request->baseUrl.'/images/help/homepage.png',
		  			'VCIS Homepage'
		  		),
				Yii::app()->request->baseUrl.'/images/help/homepage.png',
				array(
					'class'=>'fancy'
				)
			); ?>
		    <p>
		    	The Homepage is what you will see first upon entering the VCIS website.
		    	Here you will see a map of UW Whitewater's campus. Each of the links
		    	along the left side of the page under the "Buildings" header represents
		    	a building on campus, and as you move the mouse over each of these links
		    	the respective location of that building will be highlighted on the campus
		    	map. If you click any of these links you will be presented with the floor
		    	map and classrooms available within that particular building.
			</p>
			<p>
				In the top right corner, you should also note the "View List" link.
				This link will present you with the list of classrooms you have added
				while browsing this website.
			</p>
			<p>
				If you wish to return to the Registrar's webpage simply	click on the
				Registrar's Office title next to the UW Whitewater logo at the top of the screen.
		    </p>
		</div>
	</li>
	<li>
		<a href="#" id="dropFloorView"><span>+</span>After you have selected a building</a>
		<div id="floorViewStuff">
			<?php echo CHtml::link(
		  		CHtml::image(
		  			Yii::app()->request->baseUrl.'/images/help/floorSelection.png',
		  			'Select a floor'
		  		),
				Yii::app()->request->baseUrl.'/images/help/floorSelection.png',
				array(
					'class'=>'fancy'
				)
			); ?>
			<p>
				After selecting a specific building from the list on the Homepage,
				you will be presented with a picture of the building front.
			</p>
			<p>
				Along the left hand side, beneath where the name of the building is
				displayed, you will find a dropdown box from which you may choose any
				available floor level within the chosen building. Once a floor level
				has been selected, you should see a "Rooms:" header appear directly
				below the floor level selection box, where you will find the list of
				all available classrooms on the selected floor level of the chosen building.
				As you move the mouse pointer over the links representing each room,
				the corresponding location of that classroom will become shaded on the
				floor map. If you select any of the classroom links you will be presented
				with a front and back photo representation of that room, along with a
				list of features and functionality.
			</p>
			<p>
				To go back to any previous screen, you may click on the corresponding
				"breadcrumb" link, which will begin to appear beneath the UW Whitewater
				logo as you navigate deeper into the site.
		    </p>
		</div>
	</li>
	<li>
		<a href="#" id="dropRoomView"><span>+</span>After selecting a room</a>
		<div id="roomViewStuff">
			<?php echo CHtml::link(
		  		CHtml::image(
		  			Yii::app()->request->baseUrl.'/images/help/roomView.png',
		  			'Viewing a room'
		  		),
				Yii::app()->request->baseUrl.'/images/help/roomView.png',
				array(
					'class'=>'fancy'
				)
			); ?>
			<p>
				Once you have selected a classroom from the list, you will be presented
				with two photo images of the classroom, one from the front perspective
				and another from the rear. These photos may be zoomed into by clicking
				on each of them in turn.
			</p>
			<p>
				Along the right hand side, there is a short section of comments concerning
				the room under the "Room Details" header, and directly below the comments
				you will find a list of all features and functionality, seperated by category,
				that may be found within the particular room.
			</p>
			<p>
				Just above that list of items, along the right hand side, you will find a
				button labeled "Add to Room List", which when clicked, will add the room
				to your favorites list.
			</p>
			<p>
				The favorites list that you create may be navigated to at any time by use
				of the link labeled "View List" in the upper right hand corner.
			</p>
			<p>
				The status of the classroom, that is whether it is currently operational or
				not, is listed in green just above the features lists along the right hand side.
			</p>
			<p>
				Once you have finished viewing the room, you may click any of the breadcrumb
				links located below the UW Whitewater logo to return to any previous page
				to continue browsing, or click on the Registrar's Office title to be returned
				to the Registrar's Office website.
			</p>
		</div>
	</li>
	<li>
		<a href="#" id="dropFavoritesList"><span>+</span>Using the favorites list</a>
		<div id="listViewStuff">
			<?php echo CHtml::link(
		  		CHtml::image(
		  			Yii::app()->request->baseUrl.'/images/help/listView.png',
		  			'Your favorites list'
		  		),
				Yii::app()->request->baseUrl.'/images/help/listView.png',
				array(
					'class'=>'fancy'
				)
			); ?>
			<p>
				Upon clicking the link to view your list of favorites, you will be presented
				with a numerically ordered list, with each list item consisting of the
				classroom number, building in which it is located, and the floor level within
				that building on which the room is located.  The prioritization of the list
				corresponds to the numerals just to the left of each list item,	the topmost
				item being of the highest priority and denoted with the number "1". To
				reorganize the priority of your classrooms, simply click and hold with your
				mouse over the list item you wish to move, and then drag the item up or down
				within the list accordingly, and then let go of the mouse button when it is
				where you want it.
			</p>
			<p>
				To remove a classroom from your list, locate the button with an "X" on it on
				the right hand side of each list item row.  Just click that button for the
				corresponding classroom you wish to remove from your list and it will be done.
			</p>
			<p>
				Just below your list of classrooms, there is a link labeled "Email this list".
				When this link is clicked, your preferred email service will open (depending
				on your browser settings), and an email will be populated with your favorites
				list for you to send to whomever you choose.
			</p>
			<p>
				Also, take note of the warning within the yellow text box below your favorites
				list which is there to inform you that your favorites list will not remain
				if you close your browser window, so be careful!
			</p>
		</div>
	</li>
</ul>