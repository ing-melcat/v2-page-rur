from launch import LaunchDescription 
from launch_ros.actions import Node 
from launch.actions import DeclareLaunchArgument, SetEnvironmentVariable, IncludeLaunchDescription
from launch_ros.parameter_descriptions import ParameterValue
from launch.substitutions import Command, LaunchConfiguration
from launch.launch_description_sources import PythonLaunchDescriptionSource

import os
from ament_index_python.packages import get_package_share_directory

from pathlib import Path

use_sim_time = LaunchConfiguration('use_sim_time', default='true')

use_sim_time_arg = DeclareLaunchArgument(
    'use_sim_time',
    default_value='true',
    description='Use simulation (Gazebo) clock if true'
)

def generate_launch_description():

    unipolitov4_description_dir = get_package_share_directory("unipolitov4_description")
    ros_distro = os.environ["ROS_DISTRO"]
    is_ignition = "True" if ros_distro == "humble" else "False"

    model_arg = DeclareLaunchArgument(
        name="model",
        default_value= os.path.join(unipolitov4_description_dir, "urdf", "unipolitov4.urdf.xacro"),
        description="Absolute path to robot URDF file"

    )

    robot_description = ParameterValue(Command([
        "xacro ", 
        LaunchConfiguration("model"),
        " is_ignition:=", 
        is_ignition
        ]), 
        value_type=str)

    robot_state_publisher = Node(
        package= "robot_state_publisher", 
        executable = "robot_state_publisher",
        parameters=[{"robot_description" : robot_description,
                     "use_sim_time": use_sim_time}]
    )

    gazebo_resource_path = SetEnvironmentVariable(
        name="GZ_SIM_RESOURCE_PATH",
        value=[
            str(Path(unipolitov4_description_dir).parent.resolve())
            ]
    )

    #Start an empty gazebo world in a gz instance 
    gazebo = IncludeLaunchDescription(PythonLaunchDescriptionSource([
        os.path.join(
            get_package_share_directory("ros_gz_sim"), "launch"), "/gz_sim.launch.py"]),
            launch_arguments=[
                ("gz_args", [" -v 4", " -r", " empty.sdf"])
            ]
        )
    
    #start the simulation or our robot in the created world 
    gz_spawn_entity = Node(
        package="ros_gz_sim",
        executable="create",
        output="screen",
        arguments=["-topic", "robot_description", 
                   "-name", "unipolitov4"],
        parameters=[{"use_sim_time": use_sim_time}]

    )

    # Create the clock bridge
    bridge = Node(
        package='ros_gz_bridge',
        executable='parameter_bridge',
        arguments=['/clock@rosgraph_msgs/msg/Clock[gz.msgs.Clock'],
        output='screen'
    )

    return LaunchDescription([
        model_arg, 
        robot_state_publisher, 
        gazebo_resource_path, 
        gazebo, 
        gz_spawn_entity,
        bridge
    ])