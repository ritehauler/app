// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Keyboard, ScrollView } from "react-native";
import LinearGradient from "react-native-linear-gradient";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import {
  ButtonView,
  FloatLabelTextInput,
  ImageLoad,
  Loader
} from "../../components";
import { Strings, Colors, Images, Metrics } from "../../theme";
import { GRADIENT_START, GRADIENT_END } from "../../constants";
import { GradientButton } from "../../appComponents";
import MediaPicker from "../../util/MediaPicker";
import { userDataHelper } from "../../dataHelper";
import DataHandler from "../../util/DataHandler";
import styles from "./styles";
import Util from "../../util";

import {
  ENTITY_TYPE_ID_CUSTOMER,
  API_EDIT_PROFILE
} from "../../config/WebService";
import { userEditRequest } from "../../actions/UserActions";

class Profile extends Component {
  static propTypes = {
    userEditRequest: PropTypes.func.isRequired,
    user: PropTypes.object.isRequired
  };

  static defaultProps = {};

  constructor(props) {
    super(props);
    this.setFocusLastName = this.setFocusLastName.bind(this);
    this.setFocusEmail = this.setFocusEmail.bind(this);
    this.onPressDone = this.onPressDone.bind(this);
    this.onPressChangePassword = this.onPressChangePassword.bind(this);
    this.onPressChangePhoneNumber = this.onPressChangePhoneNumber.bind(this);
    this.selectProfileImage = this.selectProfileImage.bind(this);
    this.emptyFunction = this.emptyFunction.bind(this);
    this.onSubmitEmail = this.onSubmitEmail.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.user.url === API_EDIT_PROFILE) {
      this.loader.setLoading(nextProps.user.isFetching);
    }
    if (
      !_.isEqual(
        nextProps.user.data.auth.mobile_no,
        this.props.user.data.auth.mobile_no
      )
    ) {
      this.phoneInput.setText(nextProps.user.data.auth.mobile_no);
    }
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  onSubmitEmail() {
    Keyboard.dismiss();
  }

  onPressDone() {
    const inputFields = [
      this.firstNameInput,
      this.lastNameInput,
      this.emailInput
    ];
    if (Util.validateFields(inputFields)) {
      // set focus on first name due to keyboard hide issue if focus on password
      this.firstNameInput.focus();
      Keyboard.dismiss();
      this.sendUpdateProfileRequest();
    }
  }

  onPressChangePassword() {
    Actions.changePassword();
  }

  onPressChangePhoneNumber() {
    DataHandler.setIsChangeNumber(true);
    // update profile
    Actions.updatePhoneNumber({
      isUpdate: true,
      title: Strings.changeNumber,
      message: Strings.headerUpdateNumber
    });
  }

  setFocusLastName() {
    this.lastNameInput.focus();
  }

  setFocusEmail() {
    this.emailInput.focus();
  }

  selectProfileImage() {
    MediaPicker.showImagePicker(image => {
      if (image) {
        this.selectedImage = image;
        this.profileImage.setImageSource({ uri: image.uri });
      }
    });
  }

  emptyFunction() {}

  sendUpdateProfileRequest() {
    const { entity_id } = this.props.user.data;
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CUSTOMER,
      entity_id,
      first_name: this.firstNameInput.getText(),
      last_name: this.lastNameInput.getText(),
      email: this.emailInput.getText(),
      image: this.selectedImage,
      mobile_json: 1
    };
    this.props.userEditRequest(payload, API_EDIT_PROFILE);
  }

  selectedImage;

  _renderFirstName() {
    const { first_name } = this.props.user.data;
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.firstNameInput = ref;
        }}
        customContainerStyle={styles.firstName}
        autoCapitalize="sentences"
        onSubmitEditing={this.setFocusLastName}
        placeholder={Strings.firstName}
        valueText={first_name}
        errorType="required"
        errorMessage={Strings.errorMessageFirstName}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.lastNameInput)
        }
      />
    );
  }

  _renderLastName() {
    const { last_name } = this.props.user.data;
    return (
      <FloatLabelTextInput
        returnKeyType="next"
        ref={ref => {
          this.lastNameInput = ref;
        }}
        customContainerStyle={styles.lastName}
        autoCapitalize="sentences"
        placeholder={Strings.lastName}
        valueText={last_name}
        onSubmitEditing={this.setFocusEmail}
        errorType="required"
        errorMessage={Strings.errorMessageLastName}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.lastNameInput)
        }
      />
    );
  }

  _renderPhone() {
    const { auth } = this.props.user.data;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.phoneInput = ref;
        }}
        placeholder={Strings.phone}
        editable={false}
        onPress={this.onPressChangePhoneNumber}
        disableRipple={false}
        pointerEvents="none"
        valueText={auth.mobile_no || ""}
        rightImage={Images.navigation}
      />
    );
  }

  _renderEmail() {
    const { auth } = this.props.user.data;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.emailInput = ref;
        }}
        returnKeyType="done"
        keyboardType="email-address"
        placeholder={Strings.emailAddress}
        valueText={auth.email || ""}
        errorType="email"
        onSubmitEditing={this.onSubmitEmail}
        errorMessage={Strings.errorMessageEmail}
        errorMessageRequired={Strings.errorMessageEmailRequired}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.emailInput)
        }
      />
    );
  }

  _renderPassword() {
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.passInput = ref;
        }}
        secureTextEntry
        placeholder={Strings.password}
        onPress={this.onPressChangePassword}
        disableRipple={false}
        editable={false}
        pointerEvents="none"
        valueText="XXXXXXXXX"
        rightImage={Images.navigation}
      />
    );
  }

  _renderName() {
    return (
      <View style={styles.name}>
        {this._renderFirstName()}
        {this._renderLastName()}
      </View>
    );
  }

  _renderButton() {
    return <GradientButton onPress={this.onPressDone} text={Strings.done} />;
  }

  _renderGradientView() {
    return (
      <LinearGradient
        colors={Colors.background.gradient}
        start={GRADIENT_START}
        end={GRADIENT_END}
      >
        <View style={styles.headerSpace} />
      </LinearGradient>
    );
  }

  _renderImageContainer(isCustomUser) {
    const image = userDataHelper.getUserImage(this.props.user.data);
    return (
      <ButtonView
        onPress={isCustomUser ? this.selectProfileImage : this.emptyFunction}
        style={styles.imageContainer}
        disableRipple={!isCustomUser}
      >
        <ImageLoad
          isShowActivity={false}
          borderRadius={Metrics.profileImage / 2}
          style={styles.image}
          customImagePlaceholderDefaultStyle={
            styles.customImagePlaceholderDefaultStyle
          }
          placeholderSource={Images.userPlaceholder}
          source={{ uri: image }}
          ref={ref => {
            this.profileImage = ref;
          }}
        />
      </ButtonView>
    );
  }

  _renderForm(isCustomUser) {
    return (
      <View style={styles.formContainer}>
        {this._renderName()}
        {this._renderEmail()}
        {this._renderPhone()}
        {isCustomUser && this._renderPassword()}
      </View>
    );
  }

  _renderLoading() {
    return (
      <Loader
        ref={ref => {
          this.loader = ref;
        }}
      />
    );
  }

  render() {
    const { data } = this.props.user;
    const isCustomUser = userDataHelper.isCustomUser(data);
    return (
      <View style={styles.container}>
        <ScrollView
          style={styles.scroll}
          bounces={false}
          keyboardShouldPersistTaps="handled"
          ref={ref => {
            this.scrollView = ref;
          }}
        >
          {this._renderGradientView()}
          {this._renderForm(isCustomUser)}
          {this._renderImageContainer(isCustomUser)}
        </ScrollView>
        {this._renderButton()}
        {this._renderLoading()}
      </View>
    );
  }
}

const mapStateToProps = store => ({
  user: store.user
});
const actions = { userEditRequest };

export default connect(
  mapStateToProps,
  actions
)(Profile);
