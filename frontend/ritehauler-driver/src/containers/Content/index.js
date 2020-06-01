// @flow
import React, { Component } from "react";
import { Keyboard, ScrollView } from "react-native";
import HTMLView from "react-native-htmlview";
import { connect } from "react-redux";
import { ENTITY_TYPE_ID_CMS } from "../../constant";
import { Text, LoadingRequest } from "../../components";
import styles from "./styles";
import WithLoader from "../HOC/WithLoader";
// redux imports
import { request as cmsContentRequest } from "../../actions/CmsContentActions";

class Content extends Component {
  constructor(props) {
    super(props);
    this.fetchData = this.fetchData.bind(this);
  }

  componentDidMount() {
    this.fetchData();
  }

  fetchData() {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CMS,
      slug: this.props.slug
    };
    this.props.cmsContentRequest(payload);
  }

  renderContent() {
    const { cmsContent } = this.props;
    if (!cmsContent.data.length) return null;
    const htmlContent = cmsContent.data[0].desc.replace(/(\r\n|\n|\r)/gm, "");
    return (
      <HTMLView
        value={htmlContent}
        stylesheet={{ p: styles.webStyle }}
        addLineBreaks={false}
      />
    );
  }

  render() {
    if (this.props.cmsContent.isFetching) {
      return <LoadingRequest />;
    }
    return (
      <ScrollView
        style={styles.scroll}
        contentContainerStyle={styles.contentContainerStyle}
      >
        {this.renderContent()}
      </ScrollView>
    );
  }
}

const mapStateToProps = ({ cmsContent }) => ({ cmsContent });
const actions = { cmsContentRequest };

export default connect(mapStateToProps, actions)(Content);
