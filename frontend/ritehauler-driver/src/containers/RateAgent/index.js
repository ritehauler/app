//@flow
import _ from "lodash";
import React, { Component } from "react";
import { View, SafeAreaView, Keyboard } from "react-native";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import { Actions } from "react-native-router-flux";
import {
  FlatList,
  Spacer,
  Separator,
  BottomButton,
  FloatLabelTextInputMultiline,
  SectionText,
  Rating,
  Loading
} from "../../components";
import { Metrics } from "../../theme";
import styles from "./styles";
import RateAgentItem from "./RateAgentItem";
import WithLoader from "../HOC/WithLoader";
import { USER_ENTITY_TYPE_ID, ENTITY_TYPE_ID_MY_ORDERS } from "../../constant";
import Utils from "../../util";
// redux imports
import { request as ratingsListRequest } from "../../actions/RatingsListActions";
import { request as submitRatingsRequest } from "../../actions/SubmitRatingsActions";
import { selectCachedLoginUser } from "../../reducers/reduxSelectors";

class RateAgent extends Component {
  static propTypes = {
    defaultData: PropTypes.array
  };

  static defaultProps = {
    defaultData: []
  };

  /*
  static defaultProps = {
    defaultData: [
      {
        id: 1,
        title: "Polite",
        value: "polite"
      },
      {
        id: 2,
        title: "Average",
        value: "average"
      },
      {
        id: 3,
        title: "Rude",
        value: "rude"
      },
      {
        id: 4,
        title: "Abusive",
        value: "abusive"
      },
      {
        id: 5,
        title: "Others",
        value: "others"
      }
    ]
  };
  */

  constructor(props) {
    super(props);
    this._keyboardDidHide = this._keyboardDidHide.bind(this);
    this._keyboardDidShow = this._keyboardDidShow.bind(this);
    this._onDonePress = this._onDonePress.bind(this);

    this.state = {
      data: props.defaultData,
      starCount: 0,
      isOtherSelected: false,
      selectedValues: []
    };
  }
  static getDerivedStateFromProps(props, state) {
    if (props.data.length && !_.isEqual(state.data, props.data))
      return {
        data: props.data
      };

    // indicates no change in state
    return null;
  }

  componentDidMount() {
    this.keyboardDidShowListener = Keyboard.addListener(
      "keyboardDidShow",
      this._keyboardDidShow
    );
    this.keyboardDidHideListener = Keyboard.addListener(
      "keyboardDidHide",
      this._keyboardDidHide
    );
    this.props.ratingsListRequest({
      attribute_code: "customer_review"
    });
  }

  componentWillUnmount() {
    this.keyboardDidShowListener.remove();
    this.keyboardDidHideListener.remove();
  }

  shouldComponentUpdate(nextProps, nextState) {
    return (
      !_.isEqual(nextProps, this.props) || !_.isEqual(nextState, this.state)
    );
  }

  _keyboardDidShow() {
    this.setState({
      keyboardVisible: true
    });
  }

  _keyboardDidHide() {
    this.setState({
      keyboardVisible: false
    });
  }

  renderList() {
    return (
      <FlatList
        data={this.state.data}
        renderItem={this.renderItem}
        keyExtractor={item => item.title}
        ListHeaderComponent={this.renderHeader()}
        ListFooterComponent={this.renderFooter()}
        ItemSeparatorComponent={this.renderListSeparator}
        showsVerticalScrollIndicator={false}
        style={styles.list}
        contentContainerStyle={styles.listContainer}
      />
    );
  }

  renderHeader() {
    if (!this.state.keyboardVisible) {
      const { headerContainerStyle, rateContainerStyle } = styles;

      return (
        <View style={headerContainerStyle}>
          <View style={rateContainerStyle}>
            <Rating
              selectedStar={rating => this.onStarRatingPress(rating)}
              starSize={Metrics.ratingStarSize}
              rating={this.state.starCount}
            />
          </View>
          <View style={{ paddingLeft: Metrics.baseMargin }}>
            <SectionText title="Rate Behavior" />
          </View>
        </View>
      );
    }
    return null;
  }

  onStarRatingPress(rating) {
    this.setState({ starCount: rating });
  }

  renderFooter() {
    return <View>{this.renderFeedBack()}</View>;
  }

  renderFeedBack() {
    if (!this.state.isOtherSelected) {
      return null;
    }

    return (
      <View style={styles.feedbackContainerStyle}>
        <FloatLabelTextInputMultiline
          ref={ref => {
            this.feedbackInput = ref;
          }}
          placeholder="Feedback"
          customContainerStyle={styles.feedbackStyle}
          multiline
          autoFocus
          floatingStyle={styles.labelStyle}
          autoCorrect={false}
        />
      </View>
    );
  }

  renderItem = ({ item, index }) => {
    return (
      <RateAgentItem
        rateAgentItem={item}
        index={index}
        onPress={this.onPressItem}
      />
    );
  };

  onPressItem = index => {
    let data = _.cloneDeep(this.state.data);
    let selectedValues = _.cloneDeep(this.state.selectedValues);

    selectedValues.indexOf(data[index].value) >= 0
      ? selectedValues.splice(selectedValues.indexOf(data[index].value), 1)
      : selectedValues.push(data[index].value);

    data[index].isSelected = data[index].isSelected ? false : true;

    isSelect = data[index].value === "other";

    if (isSelect) {
      data.map(item => {
        item.isSelected = false;
        return item;
      });
      selectedValues = [];
    }

    this.setState({
      data,
      isOtherSelected: isSelect,
      selectedValues
    });
  };

  renderListSeparator() {
    return (
      <View style={styles.listSeparatorStyle}>
        <Spacer style={styles.spaceSeparatorStyle} />
      </View>
    );
  }

  renderDoneButton() {
    return (
      <BottomButton
        title="Done"
        style={styles.button}
        onPress={this._onDonePress}
      />
    );
  }

  _onDonePress() {
    if (
      this.state.starCount &&
      (this.state.selectedValues.length || this.state.isOtherSelected)
    ) {
      this.props.submitRatingsRequest({
        target_entity_type_id: ENTITY_TYPE_ID_MY_ORDERS,
        actor_entity_type_id: USER_ENTITY_TYPE_ID,
        target_entity_id: this.props.orderID,
        actor_entity_id: this.props.user.entity_id,
        rating: this.state.starCount,
        review: this.state.isOtherSelected ? this.feedbackInput.getText() : "",
        json_data: this.state.isOtherSelected
          ? ""
          : this.state.selectedValues.join(",")
      });
    } else {
      Utils.showMessage("please assign rating and behavior");
    }
  }

  renderLoadingModal() {
    if (this.props.submitRatingsFetching) {
      return <Loading loading={this.props.submitRatingsFetching} />;
    }
  }

  render() {
    return (
      <SafeAreaView style={styles.containerStyle}>
        <View style={styles.containerStyle}>
          {this.renderList()}
          {this.renderDoneButton()}
          {this.renderLoadingModal()}
        </View>
      </SafeAreaView>
    );
  }
}

const mapStateToProps = (
  { ratingsList, user, todaysOrders, submitRatings },
  ownProps
) => {
  return {
    orderID: ownProps.orderID
      ? ownProps.orderID
      : todaysOrders.data[0].entity_id,
    submitRatingsFetching: submitRatings.isFetching,
    componentData: {
      ...ratingsList
    },
    user: selectCachedLoginUser(user.data)
  };
};
const actions = { ratingsListRequest, submitRatingsRequest };

export default connect(mapStateToProps, actions)(WithLoader(RateAgent));
