// @flow
import React from "react";
import PropTypes from "prop-types";
import { Image, View, TextInput } from "react-native";

import { Images, Colors } from "../../theme";
import { ButtonView, Text } from "../../components";
import styles from "./styles";

export default class SearchBar extends React.PureComponent {
  static propTypes = {
    searchText: PropTypes.string,
    hasRightView: PropTypes.bool,
    rightText: PropTypes.string,
    rightTextColor: PropTypes.string,
    rightImage: PropTypes.number,
    onRightViewPress: PropTypes.func,
    onSearchTextChange: PropTypes.func,
    placeHolder: PropTypes.string
  };

  static defaultProps = {
    searchText: "",
    hasRightView: false,
    rightText: "",
    rightTextColor: "primary",
    rightImage: Images.arrowDownDark,
    onRightViewPress: () => {},
    onSearchTextChange: () => {},
    placeHolder: ""
  };

  constructor(props) {
    super(props);
    this.onChangeSearchText = this.onChangeSearchText.bind(this);
  }

  state = {
    rightText: this.props.rightText,
    rightImage: this.props.rightImage,
    searchText: this.props.searchText,
    rightTextColor: this.props.rightTextColor,
    placeHolder: this.props.placeHolder
  };

  onChangeSearchText = text => {
    const { onSearchTextChange } = this.props;
    this.setState({ searchText: text });
    onSearchTextChange(text);
  };

  getSearchText = () => this.state.searchText;

  setPlaceholder = placeHolder => {
    this.setState({ placeHolder });
  };

  setSearchText = searchText => {
    this.setState({ searchText });
  };

  setRightText = rightText => {
    this.setState({ rightText });
  };

  setRightTextColor = rightTextColor => {
    this.setState({ rightTextColor });
  };

  setRightTextAndImage = (rightText, rightImage) => {
    this.setState({ rightText, rightImage });
  };

  setRightImageAndColor = (rightImage, rightTextColor) => {
    this.setState({ rightImage, rightTextColor });
  };

  setRightTextImageAndColor = (rightText, rightImage, rightTextColor) => {
    this.setState({ rightText, rightImage, rightTextColor });
  };

  setTextRightTextImageAndColor = (
    searchText,
    rightText,
    rightImage,
    rightTextColor
  ) => {
    this.setState({ searchText, rightText, rightImage, rightTextColor });
  };

  setValues = (
    searchText,
    rightText,
    rightImage,
    rightTextColor,
    placeHolder
  ) => {
    this.setState({
      searchText,
      rightText,
      rightImage,
      rightTextColor,
      placeHolder
    });
  };

  _renderLeftView() {
    const { searchText, placeHolder } = this.state;
    return (
      <View style={styles.leftView}>
        <Image source={Images.search} />
        <TextInput
          autoCorrect={false}
          underlineColorAndroid="transparent"
          placeholder={placeHolder}
          style={styles.textInput}
          placeholderTextColor={Colors.text.searchLabel}
          value={searchText}
          onChangeText={this.onChangeSearchText}
          selectionColor={Colors.accent}
        />
        {searchText !== "" && (
          <ButtonView
            style={styles.crossContainer}
            onPress={() => this.onChangeSearchText("")}
          >
            <Image source={Images.crossSearch} style={styles.cross} />
          </ButtonView>
        )}
      </View>
    );
  }

  _renderSeparator() {
    return <View style={styles.separator} />;
  }

  _renderRightView() {
    const { rightText, rightImage, rightTextColor } = this.state;
    const { onRightViewPress } = this.props;
    return (
      <ButtonView
        style={styles.rightView}
        onPress={onRightViewPress}
        enableClick
      >
        <Text style={styles.rightText} size="xSmall" color={rightTextColor}>
          {rightText}
        </Text>
        <Image source={rightImage} />
      </ButtonView>
    );
  }

  render() {
    const { hasRightView } = this.props;
    return (
      <View style={styles.container}>
        {this._renderLeftView()}
        {hasRightView && this._renderSeparator()}
        {hasRightView && this._renderRightView()}
      </View>
    );
  }
}
