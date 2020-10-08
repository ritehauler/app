// @flow
import { connect } from "react-redux";
import React, { Component } from "react";
import {
  View,
  ScrollView,
  StatusBar,
  Image,
  Animated,
  SafeAreaView,
  TouchableOpacity,
  Keyboard
} from "react-native";
import PropTypes from "prop-types";
import _ from "lodash";
import { FloatLabelTextInput, ImageView, BottomButton } from "../../components";
import LinearGradient from "react-native-linear-gradient";
import { Images, Colors, Metrics } from "../../theme";
import { Actions } from "react-native-router-flux";
import styles from "./styles";
import { UserPresenter } from "../../presenter";
import Utils from "../../util";
import WithLoader from "../HOC/WithLoader";
import {
  WithKeyboardSubscription,
  WithKeyboardUnSubscription
} from "../HOC/WithKeyboardListener";
import {
  selectLoginUser,
  selectCachedLoginUser
} from "../../reducers/reduxSelectors";
import { profile } from "../../navigator/Keys";

class Profile extends Component {
  constructor(props) {
    super(props);
    this.scaleAnimation = new Animated.Value(0);
    this.anim1 = new Animated.Value(0);
    this.anim2 = new Animated.Value(0);
    this.anim3 = new Animated.Value(0);
    this.anim4 = new Animated.Value(0);
    this.anim5 = new Animated.Value(0);
    this.triggerScaleAnimation = this.triggerScaleAnimation.bind(this);
    this.onChangeTextInput = this.onChangeTextInput.bind(this);
  }

  shouldComponentUpdate(nextProps, nextState) {
    if (Actions.currentScene !== profile) {
      return false;
    } else {
      return (
        !_.isEqual(this.props.data, nextProps.data) ||
        !_.isEqual(this.state, nextState)
      );
    }
  }

  state = {
    isKeyboardVisible: false,
    firstRender: true
  };

  componentDidMount() {
    Animated.sequence([
      Animated.timing(this.anim4, {
        toValue: 1,
        duration: 200,
        useNativeDriver: true
      }),
      Animated.timing(this.anim1, {
        toValue: 1,
        duration: 200,
        useNativeDriver: true
      }),
      Animated.timing(this.anim2, {
        toValue: 1,
        duration: 200,
        useNativeDriver: true
      }),
      Animated.timing(this.anim3, {
        toValue: 1,
        duration: 200,
        useNativeDriver: true
      }),
      Animated.timing(this.anim5, {
        toValue: 1,
        duration: 200,
        useNativeDriver: true
      })
    ]).start();
    WithKeyboardSubscription(this);
  }

  componentWillUnmount() {
    WithKeyboardUnSubscription(this);
  }

  triggerScaleAnimation() {
    Animated.timing(this.scaleAnimation, {
      toValue: 1,
      duration: 400,
      useNativeDriver: true
    }).start();
  }

  onChangeTextInput() {
    this.setState({ dataChanged: true });
  }

  renderUserImage() {
    if (!this.state.isKeyboardVisible) {
      const {
        userImageContainerStyle,
        userImageContainerView1Style,
        userImageContainerView2Style,
        imageContainerStyle,
        imageStyle
      } = styles;

      const imageSource = this.props.data.image
        ? Utils.getValidImage(this.props.data.image)
        : Images.account_empty;

      if (!this.props.data.image) {
        this.triggerScaleAnimation();
      }
      return (
        <View>
          <View style={userImageContainerStyle}>
            <LinearGradient
              colors={Colors.lgColArray}
              start={{ x: 0.0, y: 0 }}
              end={{ x: 0.8, y: 0 }}
              style={userImageContainerView1Style}
            />
            <View style={userImageContainerView2Style} />
          </View>
          <View style={imageContainerStyle}>
            <Animated.Image
              onLoadEnd={() => {
                this.triggerScaleAnimation();
              }}
              style={[
                imageStyle,
                {
                  opacity: this.scaleAnimation,
                  transform: [
                    {
                      scale: this.scaleAnimation
                    }
                  ],
                  borderRadius: Metrics.profileImageRadius
                }
              ]}
              source={imageSource}
              loadingIndicatorSource={Images.personPlaceholder}
            />
          </View>
        </View>
      );
    }
    return null;
  }

  renderUserDetail() {
    const { userDetailContainerStyle } = styles;
    const {
      first_name,
      last_name,
      email,
      address,
      mobile_no
    } = this.props.data;
    return (
      <View style={userDetailContainerStyle}>
        {this.renderName(
          this.state.firstRender ? first_name : this.firstNameInput.getText(),
          this.state.firstRender ? last_name : this.lastNameInput.getText()
        )}
        {this.renderInput(
          "Phone",
          this.state.firstRender ? mobile_no : this.refs.phone.getText(),
          false,
          this.anim1,
          "phone"
        )}
        {this.renderInput(
          "Email",
          this.state.firstRender ? email : this.refs.email.getText(),
          false,
          this.anim2,
          "email"
        )}
        {this.renderChangePassword(
          "Change Password",
          "11111111",
          true,
          this.anim3
        )}
      </View>
    );
  }

  renderName(firstName, lastName) {
    const { userNameContainerStyle, firstNameStyle, lastNameStyle } = styles;

    return (
      <Animated.View
        style={[
          userNameContainerStyle,
          {
            opacity: this.anim4,
            transform: [
              {
                translateY: this.anim4.interpolate({
                  inputRange: [0, 1],
                  outputRange: [-50, 0],
                  extrapolate: "clamp"
                })
              }
            ]
          }
        ]}
      >
        <FloatLabelTextInput
          returnKeyType="next"
          ref={ref => {
            this.firstNameInput = ref;
          }}
          editable={false}
          blurOnSubmit={false}
          onSubmitEditing={() => this.lastNameInput.focus()}
          placeholder="First name"
          autoCapitalize="words"
          customContainerStyle={firstNameStyle}
          value={firstName}
          onChangeTextInput={this.onChangeTextInput}
        />
        <FloatLabelTextInput
          editable={false}
          returnKeyType="next"
          ref={ref => {
            this.lastNameInput = ref;
          }}
          blurOnSubmit={false}
          onSubmitEditing={() => this.refs.phone.focus()}
          placeholder="Last name"
          autoCapitalize="words"
          customContainerStyle={lastNameStyle}
          value={lastName}
          onChangeTextInput={this.onChangeTextInput}
        />
      </Animated.View>
    );
  }

  renderInput(placeholder, value, entry, animation, reference) {
    return (
      <Animated.View
        style={{
          opacity: animation,
          transform: [
            {
              translateY: animation.interpolate({
                inputRange: [0, 1],
                outputRange: [-50, 0],
                extrapolate: "clamp"
              })
            }
          ]
        }}
      >
        <FloatLabelTextInput
          editable={false}
          ref={reference}
          placeholder={placeholder}
          value={value}
          blurOnSubmit={reference === "email" ? true : false}
          onSubmitEditing={() => this.refs.email.focus()}
          returnKeyType={reference === "phone" ? "next" : "done"}
          secureTextEntry={entry}
          onChangeTextInput={this.onChangeTextInput}
        />
      </Animated.View>
    );
  }

  renderChangePassword(placeholder, value, entry, animation) {
    return (
      <TouchableOpacity onPress={Actions.changePassword}>
        <Animated.View
          style={{
            opacity: animation,
            transform: [
              {
                translateY: animation.interpolate({
                  inputRange: [0, 1],
                  outputRange: [-50, 0],
                  extrapolate: "clamp"
                })
              }
            ]
          }}
        >
          <FloatLabelTextInput
            editable={false}
            placeholder={placeholder}
            value={value}
            blurOnSubmit
            editable={false}
            secureTextEntry={entry}
          />
        </Animated.View>
      </TouchableOpacity>
    );
  }

  render() {
    const AnimatedBottomButton = Animated.createAnimatedComponent(BottomButton);
    return (
      <ScrollView
        style={styles.containerStyle}
        bounces={false}
        contentContainerStyle={{ flex: 1 }}
        keyboardShouldPersistTaps="handled"
      >
        {this.renderUserImage()}
        {this.renderUserDetail()}
        {!this.state.isKeyboardVisible &&
          this.state.dataChanged && (
            <View
              style={{
                flex: 1,
                justifyContent: "flex-end"
              }}
            >
              <AnimatedBottomButton
                title="Done"
                onPress={() => Actions.pop()}
                style={{
                  opacity: this.anim5,
                  transform: [{ scale: this.anim5 }],
                  marginBottom: Utils.isPhoneX() ? Metrics.doubleBaseMargin : 0
                }}
              />
            </View>
          )}
      </ScrollView>
    );
  }

  componentDidUpdate(prevProps, prevState) {
    this.state.firstRender = false;
  }
}

const mapStateToProps = ({ user }) => {
  const componentData = { ...user, data: selectCachedLoginUser(user.data) };
  return {
    componentData,
    modal: true
  };
};

const actions = {};

export default connect(mapStateToProps, actions)(WithLoader(Profile));
