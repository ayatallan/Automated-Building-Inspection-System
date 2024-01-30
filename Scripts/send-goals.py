#!/usr/bin/env python

import rospy
from geometry_msgs.msg import Twist
from move_base_msgs.msg import MoveBaseAction, MoveBaseGoal
import actionlib
from actionlib_msgs.msg import *

class GoForwardAndTurn():
    def __init__(self):
        rospy.init_node('nav_test', anonymous=False)

        # Initialize action client
        self.move_base = actionlib.SimpleActionClient("move_base", MoveBaseAction)
        rospy.loginfo("Waiting for the action server to come up")
        self.move_base.wait_for_server(rospy.Duration(5))

        # Define and send the first goal to move forward by 3 meters with reduced speed
        first_goal = MoveBaseGoal()
        first_goal.target_pose.header.frame_id = 'base_link'
        first_goal.target_pose.header.stamp = rospy.Time.now()
        first_goal.target_pose.pose.position.x = 2.5
        first_goal.target_pose.pose.orientation.w = 1
        self.move_base.send_goal(first_goal)
        success = self.move_base.wait_for_result(rospy.Duration(60))

        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to move forward 2 meters for some reason")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base moved 2 meters forward")

        # Define and send the second goal to move left by 1 meter with reduced speed
        second_goal = MoveBaseGoal()
        second_goal.target_pose.header.frame_id = 'base_link'
        second_goal.target_pose.header.stamp = rospy.Time.now()
        second_goal.target_pose.pose.position.y = 1.8
        second_goal.target_pose.pose.orientation.w = 0.5
        self.move_base.send_goal(second_goal)
        success = self.move_base.wait_for_result(rospy.Duration(60))

        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to move left 2 meters for some reason")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base moved left 2 meters")

        # Define and send the third goal to move backward by 1 meter with reduced speed
        third_goal = MoveBaseGoal()
        third_goal.target_pose.header.frame_id = 'base_link'
        third_goal.target_pose.header.stamp = rospy.Time.now()
        third_goal.target_pose.pose.position.x = -1.5
        third_goal.target_pose.pose.orientation.w = 1.0
        self.move_base.send_goal(third_goal)
        success = self.move_base.wait_for_result(rospy.Duration(60))

        if not success:
            self.move_base.cancel_goal()
            rospy.loginfo("The base failed to move backward 2 meters for some reason")
        else:
            state = self.move_base.get_state()
            if state == GoalStatus.SUCCEEDED:
                rospy.loginfo("Hooray, the base moved backward 2 meters")

    def shutdown(self):
        rospy.loginfo("Stop")

if name == '__main__':
    try:
        GoForwardAndTurn()
    except rospy.ROSInterruptException:
        rospy.loginfo("Exception thrown")